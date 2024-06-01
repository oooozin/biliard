<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrinterUpdateRequest;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Printer as PrinterModel;
use App\Models\Shop;
use App\Exceptions\PrinterNotFoundException;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\PrintConnectors\BluetoothPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;


class PrinterController extends Controller
{
    public function index()
    {
        DB::beginTransaction();
        try {

            $printer = PrinterModel::findOrFail(1);
            DB::commit();

            return $this->success('printer retrived successfully', $printer);

        } catch (Exceptions $e) {

            DB::rollBack();

            return $this->internalServerError();
        }
    }

    public function update(PrinterUpdateRequest $request, $id)
    {
        DB::beginTransaction();
        $payload = collect($request->validated());
        try {

            $printer = PrinterModel::findOrFail($id);
            $printer->update($payload->toArray());

            DB::commit();

            return $this->success('printer update successfully', $printer);
        } catch (Exceptions $e) {
            DB::rollBack();

            return $this->internalServerError();
        }
    }

    public function printInvoice($id)
    {
        $ip_address = PrinterModel::findOrFail(1);
        $shop = Shop::first()->toArray();
        $shop_name = $shop['name'];
        $shop_address = $shop['address'];
        $shop_phone = $shop['phone'];
        $shop_open = (new DateTime($shop['open_time']))->format('h:i a');
        $shop_close = (new DateTime($shop['close_time']))->format('h:i a');
        $invoice = Invoice::with([
            'customer',
            'orders.orderItems',
            'orders.orderItems.user',
            'orders.orderItems.product',
            'orders.tableNumber',
        ])->findOrFail($id)->toArray();
        $invoiceNumber = $invoice['invoice_number'];
        $checkOutDt = Carbon::parse($invoice['created_at']);
        $checkOut = $checkOutDt->format('d/m/Y h:i A');
        $orderItems = $invoice['orders']['order_items'];
        $productsTitle = [['Product', 'Qty', 'Price', 'Total']];
        $products = [];
        $totalQuantity = 0;

        foreach ($orderItems as $orderItem) {
            $productName = $orderItem['product']['name'];
            $quantity = $orderItem['qty'];
            $price = $orderItem['product']['price'];
            $total = $orderItem['total_price'];
            $products[] = [$productName, $quantity, $price, $total];

            $totalQuantity += $quantity;
        }

        $subtotal = $invoice['subtotal'];
        $tax = $invoice['tax'];
        $discount = $invoice['discount'];
        $totalAmount = $invoice['total'];
        $charge = $invoice['charge'];
        $refund = $invoice['refund'];

        try {

            // $connector = new NetworkPrintConnector($ip_address['invoice_ip'], 9100);
            $connector = new FilePrintConnector("/dev/usb/lp0");
            //$connector = new FilePrintConnector("/dev/usb/lp1");
            //$connector = new FilePrintConnector("/dev/usb/lp2");

            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            // $printer->text("=== Shop Voucher ===\n\n");

            $printer->setTextSize(1, 1);
            $printer->text($shop_name."\n");
            $printer->text($shop_address."\n");
            $printer->text("09-$shop_phone\n");
            $printer->text("Open Daily: $shop_open to $shop_close\n\n");
            $printer->text("Bill No: $invoiceNumber        $checkOut\n");

            $printer->setJustification(Printer::JUSTIFY_LEFT);

            $printer->text("------------------------------------------------\n");
            $printer->setEmphasis(true);
            foreach ($productsTitle as $item) {
                $printer->text(sprintf("%-22s %4s %10s %8s\n", $item[0], $item[1], $item[2], $item[3]));
            }
            $printer->setEmphasis(false);
            $printer->text("------------------------------------------------\n");
            foreach ($products as $item) {
                [$productName, $qty, $price, $total] = $item;
                $printer->text(sprintf("%-22s %4s %10s %8s\n", $productName, $qty, $price, $total));
            }
            $printer->text("------------------------------------------------\n");

            $printer->text("\n");
            $printer->text(sprintf("%-20s %6d %19s\n", 'Subtotal:', $totalQuantity, $subtotal));
            $printer->text(sprintf("%-20s %26s\n", 'Tax:', $tax));
            $printer->text(sprintf("%-20s %26s\n", 'Discount:', $discount));
            $printer->text(sprintf("%-20s %26s\n", 'Total:', $totalAmount));
            $printer->text(sprintf("%-20s %26s\n", 'Charge:', $charge));
            $printer->text(sprintf("%-20s %26s\n", 'Refund:', $refund));
            $printer->text("\n");

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("------------------------------------------------\n");
            $printer->text("Thank You For Dining With Us\n");
            $printer->text('See You Again');
            $printer->text("\n\n");
            // $printer->text("----");

            $printer->cut();
            $printer->close();

            return $this->success('Print Successfully');
        } catch (Exception $e) {
            $this->internalServerError();
        }
    }

    public function printKitchen($id)
    {
        $ip_address = PrinterModel::findOrFail(1);
        $order = Order::with(['orderItems', 'orderItems.product', 'tableNumber'])->findOrFail($id)->toArray();

        $orderItems = collect($order['order_items'])->where('status', 'SELECTED')->toArray();
        $filteredItems = collect($orderItems)->filter(function ($item) {
            return $item['product']['category_id'] == 2;
        })->toArray();
        $tableNumber = $order['table_number']['name'];
        $date = (new DateTime($order['checkin']))->format('d/m/Y h:i a');
        $productsName = [['Name', 'Qty']];
        $products = [];

        foreach ($filteredItems as $orderItem) {
            $productName = $orderItem['product']['name'];
            $quantity = $orderItem['qty'];
            $products[] = [$productName, $quantity];
        }

        try {
            $connector = new NetworkPrintConnector($ip_address['kitchen_ip'], 9100);
            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            $printer->setTextSize(2, 2);
            $printer->text("$tableNumber\n\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setTextSize(1, 1);
            $printer->text("------------------------------------------------\n");
            $printer->setTextSize(2, 1);
            $printer->setEmphasis(true);
            foreach ($productsName as $item) {
                $printer->text(sprintf("%-14s %4s\n\n", $item[0], $item[1]));
            }
            $printer->setEmphasis(false);
            $printer->setTextSize(1, 1);
            $printer->text("------------------------------------------------\n");
            $printer->setTextSize(2, 1);
            foreach ($products as $item) {
                [$productName, $quantity] = $item;
                $printer->text(sprintf("%-14s %4s\n\n", $productName, $quantity));
            }
            $printer->text("\n\n");
            $printer->setTextSize(1, 1);
            $printer->text("------------------------------------------------\n");
            $printer->text("date: $date     (kitchen)\n\n");
            $printer->cut();
            $printer->close();

            return $this->success('Print Successfully');
        } catch (Exception $e) {
            $this->internalServerError();
        }

    }

    public function printBar($id)
    {
        $ip_address = PrinterModel::findOrFail(1);
        $order = Order::with(['orderItems', 'orderItems.product', 'tableNumber'])->findOrFail($id)->toArray();

        $orderItems = collect($order['order_items'])->where('status', 'SELECTED')->toArray();
        $filteredItems = collect($orderItems)->filter(function ($item) {
            return $item['product']['category_id'] == 1;
        })->toArray();
        $tableNumber = $order['table_number']['name'];
        $date = (new DateTime($order['checkin']))->format('d/m/Y h:i a');
        $productsName = [['Name', 'Qty']];
        $products = [];

        foreach ($filteredItems as $orderItem) {
            $productName = $orderItem['product']['name'];
            $quantity = $orderItem['qty'];
            $products[] = [$productName, $quantity];
        }

        try {
            $connector = new NetworkPrintConnector($ip_address['bar_ip'], 9100);
            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);

            $printer->setTextSize(2, 2);
            $printer->text("$tableNumber\n\n");
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->setTextSize(1, 1);
            $printer->text("------------------------------------------------\n");
            $printer->setTextSize(2, 1);
            $printer->setEmphasis(true);
            foreach ($productsName as $item) {
                $printer->text(sprintf("%-14s %4s\n\n", $item[0], $item[1]));
            }
            $printer->setEmphasis(false);
            $printer->setTextSize(1, 1);
            $printer->text("------------------------------------------------\n");
            $printer->setTextSize(2, 1);
            foreach ($products as $item) {
                [$productName, $quantity] = $item;
                $printer->text(sprintf("%-14s %4s\n\n", $productName, $quantity));
            }
            $printer->text("\n\n");
            $printer->setTextSize(1, 1);
            $printer->text("------------------------------------------------\n");
            $printer->text("date: $date     (bar)\n\n");
            $printer->cut();
            $printer->close();

            return $this->success('Print Successfully');
        } catch (Exception $e) {
            $this->internalServerError();
        }

    }

    public function test()
    {
        try {
            // Enter the device file for your USB printer here
            // $connector = new NetworkPrintConnector("10.x.x.x", 9100);
            $connector = new BluetoothPrintConnector("DC:0D:30:23:10:2A");
            // $connector = new FilePrintConnector("/dev/usb/lp0");
            //$connector = new FilePrintConnector("/dev/usb/lp1");
            //$connector = new FilePrintConnector("/dev/usb/lp2");
        
            /* Print a "Hello world" receipt" */
            $printer = new Printer($connector);
            $printer -> text("Hello World!\n");
            $printer -> cut();
        
            /* Close printer */
            $printer -> close();
        } catch (Exception $e) {
            echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
        }
    }
}

<?php

use App\Enums\PermissionEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\InvoiceItemController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PrinterController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TableNumberController;
use App\Http\Controllers\ItemDataController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransferItemController;
use App\Http\Controllers\TransferMaterialController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TablePackageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/test2', [UserController::class, 'exportpdf'])->permission(PermissionEnum::ROLE_INDEX->value);
Route::get('/test', [PrinterController::class, 'test']);

Route::post('/forget-password', [PasswordResetController::class, 'forgetPassword'])->middleware('guest');
Route::get('/reset-password', [PasswordResetController::class, 'resetPasswordPage'])->middleware('guest');
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->middleware('guest');

Route::group(['prefix' => 'auth'], function () {

    Route::post('/login', [AuthController::class, 'login']);

});

Route::middleware('jwt')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'userProfile']);
    Route::post('/change-password/{id}', [AuthController::class, 'changePassword']);

    Route::group(['prefix' => 'role'], function () {
        Route::post('/import', [RoleController::class, 'import'])->permission(PermissionEnum::ROLE_UPDATE->value);
        Route::get('/exportexcel', [RoleController::class, 'exportexcel'])->permission(PermissionEnum::ROLE_INDEX->value);
        Route::get('/exportexcelparams', [RoleController::class, 'exportparams'])->permission(PermissionEnum::ROLE_INDEX->value);
        Route::get('/exportpdf', [RoleController::class, 'exportpdf'])->permission(PermissionEnum::ROLE_INDEX->value);
        Route::get('/exportpdfparams', [RoleController::class, 'exportpdfparams'])->permission(PermissionEnum::ROLE_INDEX->value);
        Route::get('/', [RoleController::class, 'index'])->permission(PermissionEnum::ROLE_INDEX->value);
        Route::post('/', [RoleController::class, 'store'])->permission(PermissionEnum::ROLE_STORE->value);
        Route::get('/{id}', [RoleController::class, 'show'])->permission(PermissionEnum::ROLE_SHOW->value);
        Route::post('/{id}', [RoleController::class, 'update'])->permission(PermissionEnum::ROLE_UPDATE->value);
        Route::delete('/{id}', [RoleController::class, 'destroy'])->permission(PermissionEnum::ROLE_DESTROY->value);
    });

    Route::group(['prefix' => 'permission'], function () {
        Route::get('/', [PermissionController::class, 'index'])->permission(PermissionEnum::PERMISSION_INDEX->value);
        Route::get('/{id}', [PermissionController::class, 'show'])->permission(PermissionEnum::PERMISSION_SHOW->value);

    });
    
    Route::group(['prefix' => 'user'], function () {
        Route::post('/assign-role', [UserController::class, 'assignRole'])->permission(PermissionEnum::USER_STORE->value);
        Route::post('/remove-role', [UserController::class, 'removeRole'])->permission(PermissionEnum::USER_UPDATE->value);
        Route::post('/import', [UserController::class, 'import'])->permission(PermissionEnum::USER_UPDATE->value);
        Route::get('/exportexcel', [UserController::class, 'exportexcel'])->permission(PermissionEnum::USER_INDEX->value);
        Route::get('/exportexcelparams', [UserController::class, 'exportparams'])->permission(PermissionEnum::USER_INDEX->value);
        Route::get('/exportpdf', [UserController::class, 'exportpdf'])->permission(PermissionEnum::USER_INDEX->value);
        Route::get('/exportpdfparams', [UserController::class, 'exportpdfparams'])->permission(PermissionEnum::USER_INDEX->value);
        Route::get('/', [UserController::class, 'index'])->permission(PermissionEnum::USER_INDEX->value);
        Route::post('/', [UserController::class, 'store'])->permission(PermissionEnum::USER_STORE->value);
        Route::get('/{id}', [UserController::class, 'show'])->permission(PermissionEnum::USER_SHOW->value);
        Route::post('/{id}', [UserController::class, 'update'])->permission(PermissionEnum::USER_UPDATE->value);
        Route::delete('/{id}', [UserController::class, 'destroy'])->permission(PermissionEnum::USER_DESTROY->value);
    });

    Route::group(['prefix' => 'shop'], function () {
        Route::post('/import', [ShopController::class, 'import'])->permission(PermissionEnum::SHOP_UPDATE->value);
        Route::get('/exportexcel', [ShopController::class, 'exportexcel'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::get('/exportexcelparams', [ShopController::class, 'exportparams'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::get('/exportpdf', [ShopController::class, 'exportpdf'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::get('/exportpdfparams', [ShopController::class, 'exportpdfparams'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::get('/', [ShopController::class, 'index'])->permission(PermissionEnum::SHOP_INDEX->value);
        Route::post('/', [ShopController::class, 'store'])->permission(PermissionEnum::SHOP_STORE->value);
        Route::get('/{id}', [ShopController::class, 'show'])->permission(PermissionEnum::SHOP_SHOW->value);
        Route::post('/{id}', [ShopController::class, 'update'])->permission(PermissionEnum::SHOP_UPDATE->value);
        Route::delete('/{id}', [ShopController::class, 'destroy'])->permission(PermissionEnum::SHOP_DESTROY->value);        
    });

    Route::group(['prefix' => 'cashier'], function () {
        Route::post('/import', [CashierController::class, 'import'])->permission(PermissionEnum::CASHIER_UPDATE->value);
        Route::get('/exportexcel', [CashierController::class, 'exportexcel'])->permission(PermissionEnum::CASHIER_INDEX->value);
        Route::get('/exportexcelparams', [CashierController::class, 'exportparams'])->permission(PermissionEnum::CASHIER_INDEX->value);
        Route::get('/exportpdf', [CashierController::class, 'exportpdf'])->permission(PermissionEnum::CASHIER_INDEX->value);
        Route::get('/exportpdfparams', [CashierController::class, 'exportpdfparams'])->permission(PermissionEnum::CASHIER_INDEX->value);
        Route::get('/', [CashierController::class, 'index'])->permission(PermissionEnum::CASHIER_INDEX->value);
        Route::post('/', [CashierController::class, 'store'])->permission(PermissionEnum::CASHIER_STORE->value);
        Route::get('/{id}', [CashierController::class, 'show'])->permission(PermissionEnum::CASHIER_SHOW->value);
        Route::post('/{id}', [CashierController::class, 'update'])->permission(PermissionEnum::CASHIER_UPDATE->value);
        Route::delete('/{id}', [CashierController::class, 'destroy'])->permission(PermissionEnum::CASHIER_DESTROY->value);
    });

    Route::group(['prefix' => 'customer'], function () {
        Route::post('/import', [CustomerController::class, 'import'])->permission(PermissionEnum::CUSTOMER_UPDATE->value);
        Route::get('/exportexcel', [CustomerController::class, 'exportexcel'])->permission(PermissionEnum::CUSTOMER_INDEX->value);
        Route::get('/exportexcelparams', [CustomerController::class, 'exportparams'])->permission(PermissionEnum::CUSTOMER_INDEX->value);
        Route::get('/exportpdf', [CustomerController::class, 'exportpdf'])->permission(PermissionEnum::CUSTOMER_INDEX->value);
        Route::get('/exportpdfparams', [CustomerController::class, 'exportpdfparams'])->permission(PermissionEnum::CUSTOMER_INDEX->value);
        Route::get('/', [CustomerController::class, 'index'])->permission(PermissionEnum::CUSTOMER_INDEX->value);
        Route::post('/', [CustomerController::class, 'store'])->permission(PermissionEnum::CUSTOMER_STORE->value);
        Route::get('/{id}', [CustomerController::class, 'show'])->permission(PermissionEnum::CUSTOMER_SHOW->value);
        Route::post('/{id}', [CustomerController::class, 'update'])->permission(PermissionEnum::CUSTOMER_UPDATE->value);
        Route::delete('/{id}', [CustomerController::class, 'destroy'])->permission(PermissionEnum::CUSTOMER_DESTROY->value);
    });

    Route::group(['prefix' => 'category'], function () {
        Route::post('/import', [CategoryController::class, 'import'])->permission(PermissionEnum::CATEGORY_UPDATE->value);
        Route::get('/exportexcel', [CategoryController::class, 'exportexcel'])->permission(PermissionEnum::CATEGORY_INDEX->value);
        Route::get('/exportexcelparams', [CategoryController::class, 'exportparams'])->permission(PermissionEnum::CATEGORY_INDEX->value);
        Route::get('/exportpdf', [CategoryController::class, 'exportpdf'])->permission(PermissionEnum::CATEGORY_INDEX->value);
        Route::get('/exportpdfparams', [CategoryController::class, 'exportpdfparams'])->permission(PermissionEnum::CATEGORY_INDEX->value);
        Route::get('/', [CategoryController::class, 'index'])->permission(PermissionEnum::CATEGORY_INDEX->value);       
        Route::post('/', [CategoryController::class, 'store'])->permission(PermissionEnum::CATEGORY_STORE->value);         
        Route::get('/{id}', [CategoryController::class, 'show'])->permission(PermissionEnum::CATEGORY_SHOW->value);          
        Route::post('/{id}', [CategoryController::class, 'update'])->permission(PermissionEnum::CATEGORY_UPDATE->value);
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->permission(PermissionEnum::CATEGORY_DESTROY->value);
    });

    Route::group(['prefix' => 'item'], function () {
        Route::post('/import', [ItemController::class, 'import'])->permission(PermissionEnum::ITEM_UPDATE->value);
        Route::get('/exportexcel', [ItemController::class, 'exportexcel'])->permission(PermissionEnum::ITEM_INDEX->value);
        Route::get('/exportexcelparams', [ItemController::class, 'exportparams'])->permission(PermissionEnum::ITEM_INDEX->value); 
        Route::get('/exportpdf', [ItemController::class, 'exportpdf'])->permission(PermissionEnum::ITEM_INDEX->value);
        Route::get('/exportpdfparams', [ItemController::class, 'exportpdfparams'])->permission(PermissionEnum::ITEM_INDEX->value);
        Route::get('/', [ItemController::class, 'index'])->permission(PermissionEnum::ITEM_INDEX->value);        
        Route::post('/', [ItemController::class, 'store'])->permission(PermissionEnum::ITEM_STORE->value);        
        Route::get('/{id}', [ItemController::class, 'show'])->permission(PermissionEnum::ITEM_SHOW->value);         
        Route::post('/{id}', [ItemController::class, 'update'])->permission(PermissionEnum::ITEM_UPDATE->value);          
        Route::patch('/{id}', [ItemController::class, 'update'])->permission(PermissionEnum::ITEM_UPDATE->value);         
        Route::delete('/{id}', [ItemController::class, 'destroy'])->permission(PermissionEnum::ITEM_DESTROY->value);          
    });

    Route::group(['prefix' => 'material'], function () {
        Route::post('/import', [MaterialController::class, 'import'])->permission(PermissionEnum::MATERIAL_UPDATE->value);
        Route::get('/exportexcel', [MaterialController::class, 'exportexcel'])->permission(PermissionEnum::MATERIAL_INDEX->value);
        Route::get('/exportexcelparams', [MaterialController::class, 'exportparams'])->permission(PermissionEnum::MATERIAL_INDEX->value); 
        Route::get('/exportpdf', [MaterialController::class, 'exportpdf'])->permission(PermissionEnum::MATERIAL_INDEX->value);
        Route::get('/exportpdfparams', [MaterialController::class, 'exportpdfparams'])->permission(PermissionEnum::MATERIAL_INDEX->value);
        Route::get('/', [MaterialController::class, 'index'])->permission(PermissionEnum::MATERIAL_INDEX->value); 
        Route::post('/', [MaterialController::class, 'store'])->permission(PermissionEnum::MATERIAL_STORE->value);         
        Route::get('/{id}', [MaterialController::class, 'show'])->permission(PermissionEnum::MATERIAL_SHOW->value);           
        Route::post('/{id}', [MaterialController::class, 'update'])->permission(PermissionEnum::MATERIAL_UPDATE->value);           
        Route::patch('/{id}', [MaterialController::class, 'update'])->permission(PermissionEnum::MATERIAL_UPDATE->value);          
        Route::delete('/{id}', [MaterialController::class, 'destroy'])->permission(PermissionEnum::MATERIAL_DESTROY->value);    
    });

    Route::group(['prefix' => 'itemData'], function () {
        Route::post('/import', [ItemDataController::class, 'import'])->permission(PermissionEnum::ITEM_DATA_UPDATE->value);
        Route::get('/exportexcel', [ItemDataController::class, 'exportexcel'])->permission(PermissionEnum::ITEM_DATA_INDEX->value);        
        Route::get('/exportexcelparams', [ItemDataController::class, 'exportparams'])->permission(PermissionEnum::ITEM_DATA_INDEX->value);         
        Route::get('/exportpdf', [ItemDataController::class, 'exportpdf'])->permission(PermissionEnum::ITEM_DATA_INDEX->value);        
        Route::get('/exportpdfparams', [ItemDataController::class, 'exportpdfparams'])->permission(PermissionEnum::ITEM_DATA_INDEX->value);        
        Route::get('/', [ItemDataController::class, 'index'])->permission(PermissionEnum::ITEM_DATA_INDEX->value);         
        Route::post('/', [ItemDataController::class, 'store'])->permission(PermissionEnum::ITEM_DATA_STORE->value);       
        Route::get('/{id}', [ItemDataController::class, 'show'])->permission(PermissionEnum::ITEM_DATA_SHOW->value);         
        Route::post('/{id}', [ItemDataController::class, 'update'])->permission(PermissionEnum::ITEM_DATA_UPDATE->value);           
        Route::patch('/{id}', [ItemDataController::class, 'update'])->permission(PermissionEnum::ITEM_DATA_UPDATE->value);          
        Route::delete('/{id}', [ItemDataController::class, 'destroy'])->permission(PermissionEnum::ITEM_DATA_DESTROY->value);              
    });

    Route::group(['prefix' => 'materialData'], function () {
        Route::post('/import', [MaterialDataController::class, 'import'])->permission(PermissionEnum::MATERIAL_DATA_UPDATE->value);
        Route::get('/exportexcel', [MaterialDataController::class, 'exportexcel'])->permission(PermissionEnum::MATERIAL_DATA_INDEX->value);       
        Route::get('/exportexcelparams', [MaterialDataController::class, 'exportparams'])->permission(PermissionEnum::MATERIAL_DATA_INDEX->value);        
        Route::get('/exportpdf', [MaterialDataController::class, 'exportpdf'])->permission(PermissionEnum::MATERIAL_DATA_INDEX->value);       
        Route::get('/exportpdfparams', [MaterialDataController::class, 'exportpdfparams'])->permission(PermissionEnum::MATERIAL_DATA_INDEX->value);       
        Route::get('/', [MaterialDataController::class, 'index'])->permission(PermissionEnum::MATERIAL_DATA_INDEX->value);        
        Route::post('/', [MaterialDataController::class, 'store'])->permission(PermissionEnum::MATERIAL_DATA_STORE->value);         
        Route::get('/{id}', [MaterialDataController::class, 'show'])->permission(PermissionEnum::MATERIAL_DATA_SHOW->value);         
        Route::post('/{id}', [MaterialDataController::class, 'update'])->permission(PermissionEnum::MATERIAL_DATA_UPDATE->value);         
        Route::patch('/{id}', [MaterialDataController::class, 'update'])->permission(PermissionEnum::MATERIAL_DATA_UPDATE->value);        
        Route::delete('/{id}', [MaterialDataController::class, 'destroy'])->permission(PermissionEnum::MATERIAL_DATA_DESTROY->value);               
    });

    Route::group(['prefix' => 'transferItem'], function () {
        Route::get('/exportexcel', [TransferItemController::class, 'exportexcel'])->permission(PermissionEnum::TRANSFER_ITEM_INDEX->value);       
        Route::get('/exportexcelparams', [TransferItemController::class, 'exportparams'])->permission(PermissionEnum::TRANSFER_ITEM_INDEX->value);        
        Route::get('/exportpdf', [TransferItemController::class, 'exportpdf'])->permission(PermissionEnum::TRANSFER_ITEM_INDEX->value);       
        Route::get('/exportpdfparams', [TransferItemController::class, 'exportpdfparams'])->permission(PermissionEnum::TRANSFER_ITEM_INDEX->value);       
        Route::get('/', [TransferItemController::class, 'index'])->permission(PermissionEnum::TRANSFER_ITEM_INDEX->value);        
        Route::post('/', [TransferItemController::class, 'store'])->permission(PermissionEnum::TRANSFER_ITEM_STORE->value);         
        Route::get('/{id}', [TransferItemController::class, 'show'])->permission(PermissionEnum::TRANSFER_ITEM_SHOW->value);           
        Route::post('/{id}', [TransferItemController::class, 'update'])->permission(PermissionEnum::TRANSFER_ITEM_UPDATE->value);            
        Route::delete('/{id}', [TransferItemController::class, 'destroy'])->permission(PermissionEnum::TRANSFER_ITEM_DESTROY->value);       
    });

    Route::group(['prefix' => 'transferMaterial'], function () {
        Route::get('/exportexcel', [TransferMaterialController::class, 'exportexcel'])->permission(PermissionEnum::TRANSFER_MATERIAL_INDEX->value);         
        Route::get('/exportexcelparams', [TransferMaterialController::class, 'exportparams'])->permission(PermissionEnum::TRANSFER_MATERIAL_INDEX->value);          
        Route::get('/exportpdf', [TransferMaterialController::class, 'exportpdf'])->permission(PermissionEnum::TRANSFER_MATERIAL_INDEX->value);         
        Route::get('/exportpdfparams', [TransferMaterialController::class, 'exportpdfparams'])->permission(PermissionEnum::TRANSFER_MATERIAL_INDEX->value);         
        Route::get('/', [TransferMaterialController::class, 'index'])->permission(PermissionEnum::TRANSFER_MATERIAL_INDEX->value);          
        Route::post('/', [TransferMaterialController::class, 'store'])->permission(PermissionEnum::TRANSFER_MATERIAL_STORE->value);        
        Route::get('/{id}', [TransferMaterialController::class, 'show'])->permission(PermissionEnum::TRANSFER_MATERIAL_SHOW->value);           
        Route::post('/{id}', [TransferMaterialController::class, 'update'])->permission(PermissionEnum::TRANSFER_MATERIAL_UPDATE->value);            
        Route::delete('/{id}', [TransferMaterialController::class, 'destroy'])->permission(PermissionEnum::TRANSFER_MATERIAL_DESTROY->value);        
    });

    Route::group(['prefix' => 'table'], function () {
        Route::post('/import', [TableNumberController::class, 'import'])->permission(PermissionEnum::TABLE_NUMBER_UPDATE->value);
        Route::get('/exportexcel', [TableNumberController::class, 'exportexcel'])->permission(PermissionEnum::TABLE_NUMBER_INDEX->value);
        Route::get('/exportexcelparams', [TableNumberController::class, 'exportparams'])->permission(PermissionEnum::TABLE_NUMBER_INDEX->value);
        Route::get('/exportpdf', [TableNumberController::class, 'exportpdf'])->permission(PermissionEnum::TABLE_NUMBER_INDEX->value);
        Route::get('/exportpdfparams', [TableNumberController::class, 'exportpdfparams'])->permission(PermissionEnum::TABLE_NUMBER_INDEX->value);
        Route::get('/', [TableNumberController::class, 'index'])->permission(PermissionEnum::TABLE_NUMBER_INDEX->value);          
        Route::post('/', [TableNumberController::class, 'store'])->permission(PermissionEnum::TABLE_NUMBER_STORE->value);          
        Route::get('/{id}', [TableNumberController::class, 'show'])->permission(PermissionEnum::TABLE_NUMBER_SHOW->value);          
        Route::post('/{id}', [TableNumberController::class, 'update'])->permission(PermissionEnum::TABLE_NUMBER_UPDATE->value);         
        Route::patch('/{id}', [TableNumberController::class, 'update'])->permission(PermissionEnum::TABLE_NUMBER_UPDATE->value);           
        Route::delete('/{id}', [TableNumberController::class, 'destroy'])->permission(PermissionEnum::TABLE_NUMBER_DESTROY->value);         
    });

    Route::group(['prefix' => 'order'], function () {
        Route::get('/', [OrderController::class, 'index'])->permission(PermissionEnum::ORDER_INDEX->value);         
        Route::post('/', [OrderController::class, 'store'])->permission(PermissionEnum::ORDER_STORE->value);           
        Route::get('/{id}', [OrderController::class, 'show'])->permission(PermissionEnum::ORDER_SHOW->value);        
        Route::post('/{id}', [OrderController::class, 'update'])->permission(PermissionEnum::ORDER_UPDATE->value);         
        Route::patch('/{id}', [OrderController::class, 'update'])->permission(PermissionEnum::ORDER_UPDATE->value);        
        Route::delete('/{id}', [OrderController::class, 'destroy'])->permission(PermissionEnum::ORDER_DESTROY->value);           
    });

    Route::group(['prefix' => 'invoice-item'], function () {
        Route::get('/', [InvoiceItemController::class, 'index'])->permission(PermissionEnum::ROLE_INDEX->value);         
        Route::post('/', [InvoiceItemController::class, 'store'])->permission(PermissionEnum::ROLE_STORE->value);        
        Route::get('/{id}', [InvoiceItemController::class, 'show'])->permission(PermissionEnum::ROLE_SHOW->value);           
        Route::post('/{id}', [InvoiceItemController::class, 'update'])->permission(PermissionEnum::ROLE_UPDATE->value);       
        Route::patch('/{id}', [InvoiceItemController::class, 'update'])->permission(PermissionEnum::ROLE_UPDATE->value);          
        Route::delete('/{id}', [InvoiceItemController::class, 'destroy'])->permission(PermissionEnum::ROLE_DESTROY->value);         
    });

    // Route::group(['prefix' => 'table-package'], function () {
    //     Route::post('/import', [TablePackageController::class, 'import'])->permission(PermissionEnum::ROLE_UPDATE->value);
    //     Route::get('/exportexcel', [TablePackageController::class, 'exportexcel'])->permission(PermissionEnum::ROLE_INDEX->value);
    //     Route::get('/exportexcelparams', [TablePackageController::class, 'exportparams'])->permission(PermissionEnum::ROLE_INDEX->value);
    //     Route::get('/exportpdf', [TablePackageController::class, 'exportpdf'])->permission(PermissionEnum::ROLE_INDEX->value);
    //     Route::get('/exportpdfparams', [TablePackageController::class, 'exportpdfparams'])->permission(PermissionEnum::ROLE_INDEX->value);
    //     Route::get('/', [TablePackageController::class, 'index'])->permission(PermissionEnum::ROLE_INDEX->value);         
    //     Route::post('/', [TablePackageController::class, 'store'])->permission(PermissionEnum::ROLE_STORE->value);        
    //     Route::get('/{id}', [TablePackageController::class, 'show'])->permission(PermissionEnum::ROLE_SHOW->value);           
    //     Route::post('/{id}', [TablePackageController::class, 'update'])->permission(PermissionEnum::ROLE_UPDATE->value);       
    //     Route::patch('/{id}', [TablePackageController::class, 'update'])->permission(PermissionEnum::ROLE_UPDATE->value);          
    //     Route::delete('/{id}', [TablePackageController::class, 'destroy'])->permission(PermissionEnum::ROLE_DESTROY->value);         
    // });

    Route::group(['prefix' => 'bill'], function () {
        Route::post('/import', [BillController::class, 'import'])->permission(PermissionEnum::BILL_UPDATE->value);
        Route::get('/exportexcel', [BillController::class, 'exportexcel'])->permission(PermissionEnum::BILL_INDEX->value);
        Route::get('/exportexcelparams', [BillController::class, 'exportparams'])->permission(PermissionEnum::BILL_INDEX->value);
        Route::get('/exportpdf', [BillController::class, 'exportpdf'])->permission(PermissionEnum::BILL_INDEX->value);
        Route::get('/exportpdfparams', [BillController::class, 'exportpdfparams'])->permission(PermissionEnum::BILL_INDEX->value);
        Route::get('/', [BillController::class, 'index'])->permission(PermissionEnum::BILL_INDEX->value);         
        Route::post('/', [BillController::class, 'store'])->permission(PermissionEnum::BILL_STORE->value);        
        Route::get('/{id}', [BillController::class, 'show'])->permission(PermissionEnum::BILL_SHOW->value);           
        Route::post('/{id}', [BillController::class, 'update'])->permission(PermissionEnum::BILL_UPDATE->value);       
        Route::patch('/{id}', [BillController::class, 'update'])->permission(PermissionEnum::BILL_UPDATE->value);          
        Route::delete('/{id}', [BillController::class, 'destroy'])->permission(PermissionEnum::BILL_DESTROY->value);         
    });

    Route::group(['prefix' => 'payment'], function () {
        Route::post('/import', [PaymentController::class, 'import'])->permission(PermissionEnum::PAYMENT_UPDATE->value);
        Route::get('/exportexcel', [PaymentController::class, 'exportexcel'])->permission(PermissionEnum::PAYMENT_INDEX->value);
        Route::get('/exportexcelparams', [PaymentController::class, 'exportparams'])->permission(PermissionEnum::PAYMENT_INDEX->value);
        Route::get('/exportpdf', [PaymentController::class, 'exportpdf'])->permission(PermissionEnum::PAYMENT_INDEX->value);
        Route::get('/exportpdfparams', [PaymentController::class, 'exportpdfparams'])->permission(PermissionEnum::PAYMENT_INDEX->value);
        Route::get('/', [PaymentController::class, 'index'])->permission(PermissionEnum::PAYMENT_INDEX->value);         
        Route::post('/', [PaymentController::class, 'store'])->permission(PermissionEnum::PAYMENT_STORE->value);        
        Route::get('/{id}', [PaymentController::class, 'show'])->permission(PermissionEnum::PAYMENT_SHOW->value);           
        Route::post('/{id}', [PaymentController::class, 'update'])->permission(PermissionEnum::PAYMENT_UPDATE->value);       
        Route::patch('/{id}', [PaymentController::class, 'update'])->permission(PermissionEnum::PAYMENT_UPDATE->value);          
        Route::delete('/{id}', [PaymentController::class, 'destroy'])->permission(PermissionEnum::PAYMENT_DESTROY->value);         
    });

    Route::group(['prefix' => 'invoice'], function () {
        Route::get('/', [InvoiceController::class, 'index'])->permission(PermissionEnum::INVOICE_INDEX->value);           
        Route::post('/', [InvoiceController::class, 'store'])->permission(PermissionEnum::INVOICE_STORE->value);         
        Route::get('/{id}', [InvoiceController::class, 'show'])->permission(PermissionEnum::INVOICE_SHOW->value);           
        Route::post('/{id}', [InvoiceController::class, 'update'])->permission(PermissionEnum::INVOICE_UPDATE->value);        
        Route::delete('/{id}', [InvoiceController::class, 'destroy'])->permission(PermissionEnum::INVOICE_DESTROY->value);          
    });

    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/', [DashboardController::class, 'getDashboardData']);
    });

    // Route::group(['prefix' => 'dashboard'], function () {
    //     Route::get('/', [DashboardController::class, 'getDashboardData'])->permission(PermissionEnum::DASHBOARD_INDEX->value);
    // });

    Route::group(['prefix' => 'printer'], function () {
        Route::get('/', [PrinterController::class, 'index'])->permission(PermissionEnum::ROLE_INDEX->value);
        Route::post('/{id}', [PrinterController::class, 'update'])->permission(PermissionEnum::ROLE_UPDATE->value);
        Route::get('/print-invoice/{id}', [PrinterController::class, 'printInvoice']);
        Route::get('/print-kitchen/{id}', [PrinterController::class, 'printKitchen']);
        Route::get('/print-bar/{id}', [PrinterController::class, 'printBar']);
    });

});

Route::get('/image/{path}', [ItemController::class, 'getImage'])->where('path', '.*');

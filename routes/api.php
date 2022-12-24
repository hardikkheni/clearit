<?php

use App\Http\Controllers\Api\{
    AuthController,
    NoteController,
    PgaRequestController,
    ResourceController,
    TicketController,
    TicketDocumentController,
    TicketUserHtsController,
    UserRoleController
};
use App\Http\Controllers\Api\Helper\{
    AffiliateController,
    AgentController,
    CustomerController,
    AlertMessageController,
    ApiUserController,
    ClientRequestController,
    DocUploadTypeController,
    FeightForwarderController,
    NotificationController,
    ReminderController,
};
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/auth/login', [AuthController::class, 'login']);


Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::prefix('auth')->group(function () {
        Route::get('profile', [AuthController::class, 'profile']);
    });

    Route::prefix('user-role')->group(function () {
        Route::post('permissions', [UserRoleController::class, 'updateUserRolePermissions']);
        Route::post('grant-revoke-agent-role', [UserRoleController::class, 'grantRevokeAgentFromRole']);
        Route::get('{id}/agents', [UserRoleController::class, 'getUserRoleAgents']);
    });


    //user group
    Route::prefix('user')->group(function () {
        Route::post('list', [AuthController::class, 'list']);
    });

    Route::prefix('helper')->group(function () {

        // agent group
        Route::group([
            'prefix' => 'agent',
        ], function () {
            Route::get('all-agents', [AgentController::class, 'allAgents']);
            Route::post('status-list', [AgentController::class, 'getAgentStatusList']);
            Route::group(['middleware' => ['permission:PERMISSION_BITMASK_AGENT_STATUSES']], function () {
                Route::post('re-order-status-list', [AgentController::class, 'reOrderAgentStatusList']);
                Route::post('status', [AgentController::class, 'upsertAgentStatus']);
                Route::delete('status/{id}', [AgentController::class, 'deleteAgentStatus']);
            });
            Route::get('all-internal-agents', [AgentController::class, 'allInternalAgents']);
            Route::get('all-permissions', [AgentController::class, 'allPermisisons']);
            Route::post('save-permissions', [AgentController::class, 'savePermissions'])->middleware('master');
            Route::group([
                'middleware' => ['permission:PERMISSION_BITMASK_AGENTS']
            ], function () {
                Route::post('', [AgentController::class, 'create']);
                Route::post('list', [AgentController::class, 'list']);
                Route::get('{id}', [AgentController::class, 'get']);
                Route::post('{id}', [AgentController::class, 'edit']);
            });
        });

        // notification groups
        Route::prefix('notification')->group(function () {
            Route::get('list', [NotificationController::class, 'list']);
            Route::post('mark-viewed', [NotificationController::class, 'markViewed']);
        });

        // client-request group
        Route::prefix('client-request')->group(function () {
            Route::get('list', [ClientRequestController::class, 'list']);
            Route::post('mark-viewed/{id}', [ClientRequestController::class, 'markViewed']);
            Route::get('daily-mails', [ClientRequestController::class, 'dailyMails']);
            Route::post('bulk-insert', [ClientRequestController::class, 'bulkInsert']);
            Route::post('{id}/mark-as-received', [ClientRequestController::class, 'markAsReceived']);
        });

        // daily-reports group
        Route::get('daily-reports', [TicketController::class, 'dailyReport']);

        // affiliate group
        Route::prefix('affiliate')->group(function () {
            Route::group([
                'middleware' => ['permission:PERMISSION_BITMASK_AFFILIATES']
            ], function () {
                Route::get('all-affiliates', [AffiliateController::class, 'allAffiliates']);
                Route::post('', [AffiliateController::class, 'create']);
                Route::post('list', [AffiliateController::class, 'list']);
                Route::post('register-affiliate', [AffiliateController::class, 'registerAffiliate']);
                Route::get('{id}', [AffiliateController::class, 'get']);
                Route::post('{id}', [AffiliateController::class, 'edit']);
                Route::delete('{id}', [AffiliateController::class, 'delete']);
            });
        });

        // reminders
        Route::prefix('reminder')->group(function () {
            Route::get('{filter}', [ReminderController::class, 'list']);
            Route::post('', [ReminderController::class, 'create']);
            Route::post('{id}', [ReminderController::class, 'edit']);
            Route::delete('{id}', [ReminderController::class, 'delete']);
            Route::patch('', [ReminderController::class, 'completeReminders']);
        });

        // customers
        Route::prefix('customer')->group(function () {
            Route::post('unverified-with-poa-data-table', [CustomerController::class, 'unverifiedWithPoaDataTable']);
            Route::post('list', [CustomerController::class, 'list']);
        });

        //doc upload type group
        Route::prefix('document-upload-type')->group(function () {
            Route::get('mode-of-transport-list', [DocUploadTypeController::class, 'modeOfTransportList']);
            Route::get('{modeId}', [DocUploadTypeController::class, 'getByMotId']);
            Route::post('', [DocUploadTypeController::class, 'upsert'])->middleware('permission:PERMISSION_BITMASK_DOC_UPLOAD_TYPES');
            Route::delete('{id}', [DocUploadTypeController::class, 'delete'])->middleware('permission:PERMISSION_BITMASK_DOC_UPLOAD_TYPES');
        });

        //freight forwarders group
        Route::prefix('freight-forwarder')->group(function () {
            Route::group(['middleware' => ['permission:PERMISSION_BITMASK_AFFILIATES']], function () {
                Route::post('', [FeightForwarderController::class, 'create']);
                Route::post('list', [FeightForwarderController::class, 'list']);
                Route::get('{id}', [FeightForwarderController::class, 'get']);
                Route::post('{id}', [FeightForwarderController::class, 'edit']);
                Route::delete('{id}', [FeightForwarderController::class, 'delete']);
            });
            Route::get('{id}/contacts', [FeightForwarderController::class, 'getContacts']);
        });

        //freight contacts group
        Route::prefix('freight-contact')->group(function () {
            Route::group(['middleware' => ['permission:PERMISSION_BITMASK_AFFILIATES']], function () {
                Route::post('{ffId}', [FeightForwarderController::class, 'createContact']);
                Route::get('{id}', [FeightForwarderController::class, 'getContact']);
                Route::post('{ffId}/{id}', [FeightForwarderController::class, 'editContact']);
                Route::delete('{id}', [FeightForwarderController::class, 'deleteContact']);
            });
        });


        //alert message group
        Route::prefix('alert-message')->group(function () {
            Route::group([
                'middleware' => ['permission:PERMISSION_BITMASK_MANAGE_ALERT_MESSAGES']
            ], function () {
                Route::post('', [AlertMessageController::class, 'create']);
                Route::post('list', [AlertMessageController::class, 'list']);
                Route::get('{id}', [AlertMessageController::class, 'get']);
                Route::post('{id}', [AlertMessageController::class, 'edit']);
                Route::delete('{id}', [AlertMessageController::class, 'delete']);
            });
        });

        //api user group
        Route::prefix('api-user')->group(function () {
            Route::post('', [ApiUserController::class, 'create']);
            Route::post('list', [ApiUserController::class, 'list']);
            Route::get('{id}', [ApiUserController::class, 'get']);
            Route::post('{id}', [ApiUserController::class, 'edit']);
        });

        Route::prefix('ticket')->group(function () {
            Route::post('status-list', [TicketController::class, 'getTicketStatusList']);
            Route::group(['middleware' => ['permission:PERMISSION_BITMASK_TICKET_STATUSES']], function () {
                Route::post('re-order-status-list', [TicketController::class, 'reOrderTicketStatusList']);
                Route::post('status', [TicketController::class, 'upsertTicketStatus']);
                Route::delete('status/{id}', [TicketController::class, 'deleteTicketStatus']);
            });
            Route::post('freightos-billing', [TicketController::class, 'freightosBillingData']);
            Route::post('to-do-ticket-item-list', [TicketController::class, 'getTodoTicketItemList']);
            Route::group(['middleware' => ['permission:PERMISSION_BITMASK_TO_DO_CHECKLISTS']], function () {
                Route::post('re-order-to-do-ticket-item-list', [TicketController::class, 'reOrderTodoTicketItemList']);
                Route::post('to-do-ticket-item', [TicketController::class, 'upsertTodoTicketItem']);
                Route::delete('to-do-ticket-item/{id}', [TicketController::class, 'deleteTodoTicketItem']);
            });
        });
    });

    Route::prefix('pga-request')->group(function () {
        Route::delete('{id}', [PgaRequestController::class, 'delete']);
    });

    Route::prefix('ticket-document')->group(function () {
        Route::post('', [TicketDocumentController::class, 'create']);
        Route::delete('{id}', [TicketDocumentController::class, 'delete']);
        Route::post('{id}/document-upload-type', [TicketDocumentController::class, 'updateDocUploadType']);
    });

    Route::prefix('ticket-user-hts')->group(function () {
        Route::post('', [TicketUserHtsController::class, 'create']);
        Route::post('{id}', [TicketUserHtsController::class, 'update']);
        Route::delete('{id}', [TicketUserHtsController::class, 'delete']);
    });

    Route::prefix('note')->group(function () {
        Route::post('', [NoteController::class, 'create']);
    });

    Route::prefix('ticket')->group(function () {
        Route::patch('{id}', [TicketController::class, 'patch']);
        Route::post('{id}/delete', [TicketController::class, 'delete']);
        Route::post('{id}/attach-user-hts', [TicketController::class, 'attachUserHts']);
        Route::post('{id}/update-notify-tariff-code', [TicketController::class, 'updateNotifyTariffCode']);
        Route::post('{id}/send-notify-tariff-code-email', [TicketController::class, 'sendNotifyTariffCodeEmail']);
        Route::post('{id}/add-eta', [TicketController::class, 'addEta']);
        Route::post('{id}/add-billing', [TicketController::class, 'addBilling']);
        Route::post('{id}/update-status', [TicketController::class, 'updateTicketStatus']);
        Route::post('{id}/add-carrier-details', [TicketController::class, 'addCarrierDetails']);
        Route::post('{id}/add-affiliate-referance', [TicketController::class, 'addAffiliateReferance']);
        Route::post('{id}/remove-affiliate-referance', [TicketController::class, 'removeAffiliateReferance']);
        Route::post('{id}/update-agent', [TicketController::class, 'updateAgent']);
        Route::post('{id}/update-processing-agent', [TicketController::class, 'updateProcessingAgent']);
        Route::post('{id}/mark-as-paid/{payId}', [TicketController::class, 'markAsPaid']);
        Route::post('{id}/add-pga-request', [TicketController::class, 'addPgaRequest']);
        Route::delete('{id}/payment/{payId}', [TicketController::class, 'removePayment']);
        Route::get('{id}/freight-invoice-item-list', [TicketController::class, 'getFreightInvoiceItemList']);
        Route::get('{id}/get-fc-datetime', [TicketController::class, 'getFcDatetime']);
        Route::get('{id}/get-fc-invoice-datetime', [TicketController::class, 'getFcInvoiceDatetime']);
        Route::delete('{invoiceItemId}/freight-invoice-item', [TicketController::class, 'deleteFreightInvoiceItem']);
        Route::post('{id}/update-require-broker-review', [TicketController::class, 'updateRequireBrokerReview']);
        Route::post('status-dependencies', [TicketController::class, 'getTicketStatusDependencies']);
        Route::put('status-dependencies', [TicketController::class, 'putTicketStatusDependencies'])
            ->middleware('permission:PERMISSION_BITMASK_TICKET_STATUS_DEPENDENCIES');
        Route::get('{role}/{guid}', [TicketController::class, 'getTicket']);
    });

    Route::post('dashboard', [AuthController::class, 'dashboard']);

    //resource group
    Route::prefix('resource')->group(function () {
        Route::get('country', [ResourceController::class, 'getAllCountries']);
        Route::get('user-roles', [UserRoleController::class, 'getUserRoles']);
        Route::get('ticket-status2', [ResourceController::class, 'getTicketStatus2']);
    });
});

<?php

namespace App\Http\Controllers\Admin;

use App\AdminCommissionHistory;
use App\Battle;
use App\Gamelisting;
use App\Http\Controllers\Controller;
use App\KycUpload;
use App\MannualTransaction;
use App\PenaltyHistory;
use App\ReferCommission;
use App\Referral;
use App\Room;
use App\RoomHistory;
use App\TransactionHistory;
use App\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DataTableController extends Controller
{
    private $tableTypes = ['Transaction'];

    public function index(Request $request)
    {
        if ($request->type) {
            switch ($request->type) {

                case 'transaction':
                    $transactionQuery = datatables()
                        ->of(TransactionHistory::select(
                            [
                                'username',
                                'user_id',
                                'transaction_amount',
                                'transaction_type',
                                'dr_cr',
                                'order_id',
                                'closing_balance',
                                'game_name',
                                'battle_id',
                                'opposition_player',
                                'u.phone_no as phone_no',
                                'transaction_histories.created_at as date',
                            ])
                                ->leftJoin('users AS u', 'transaction_histories.user_id', '=', 'u.id')->orderBy('date', 'DESC'));
                    return $transactionQuery
                        ->filter(function ($query) use ($request) {
                            if ($request->get('transaction') == '1' || $request->get('transaction') == '2' || $request->get('transaction') == '3' || $request->get('transaction') == '4' || $request->get('transaction') == '5' || $request->get('transaction') == '6') {
                                $query->where('transaction_type', $request->get('transaction'));

                            }
                            if ($request->get('debit_credit') == '0' || $request->get('debit_credit') == '1') {
                                $query->where('dr_cr', $request->get('debit_credit'));

                            }
                            if ($request->has('search')) {
                                $serchTerms = $request->search;
                                $query->where(function ($q) use ($serchTerms) {
                                    $q->where('username', 'like', "%{$serchTerms}%")
                                        ->orWhere('transaction_amount', 'like', "%{$serchTerms}%")
                                        ->orWhere('transaction_type', 'like', "%{$serchTerms}%")
                                        ->orWhere('game_name', 'like', "%{$serchTerms}%")
                                        ->orWhere('phone_no', 'like', "%{$serchTerms}%");
                                });
                            }
                        })
                        ->make(true);

                    // return DataTables::of(TransactionHistory::with('user')->orderBy('id','DESC'))
                    // ->addIndexColumn()
                    // ->addColumn('view_user', function($row){

                    //     $actionBtn = '<a class="text-primary" href="transactions/' . $row->user_id . '">'.$row->username.'</a>';
                    //     return $actionBtn;
                    // })
                    // ->filter(function ($instance) use ($request) {
                    // if ($request->get('transaction') == '1' || $request->get('transaction') == '2' || $request->get('transaction') == '3' || $request->get('transaction') == '4' || $request->get('transaction') == '5') {
                    //     $instance->where('transaction_type', $request->get('transaction'));

                    // }
                    // if ($request->get('debit_credit') == '0' || $request->get('debit_credit') == '1') {
                    //     $instance->where('dr_cr', $request->get('debit_credit'));

                    // }
                    //     if (!empty($request->get('search'))) {
                    //          $instance->where(function($w) use($request){
                    //             // print_r($w);
                    //             $search = $request->get('search');
                    //             $w->orWhere('username', 'LIKE', "%$search%")
                    //             ->orWhere('transaction_amount', 'LIKE', "%$search%")
                    //             ->orWhere('transaction_type', 'LIKE', "%$search%")
                    //             ->orWhere('game_name', 'LIKE', "%$search%")
                    //             ->orWhere('user.phone_no', 'LIKE', "%$search%");
                    //         });
                    //     }
                    // })
                    // ->rawColumns(['view_user'])
                    //     ->make(true);
                    break;
                case 'user_transaction':
                    return DataTables::of(TransactionHistory::orderBy('created_at', 'DESC')->where('user_id', $request->get('user_id')))
                        ->make(true);
                    break;

                case 'battle_transaction':
                    $battleQuery = datatables()
                        ->of(TransactionHistory::select(
                            [
                                'username',
                                'user_id',
                                'transaction_amount',
                                'transaction_type',
                                'dr_cr',
                                'order_id',
                                'closing_balance',
                                'game_name',
                                'battle_id',
                                'opposition_player',
                                'transaction_histories.created_at as date',
                                'u.phone_no as phone_no',
                            ])
                                ->leftJoin('users AS u', 'transaction_histories.user_id', '=', 'u.id')->where('transaction_histories.order_id', '=', '')->where('transaction_histories.battle_id', '!=', '')->orderBy('date', 'DESC'));
                    return $battleQuery
                        ->filter(function ($query) use ($request) {
                            if ($request->get('transaction') == '3' || $request->get('transaction') == '4' || $request->get('transaction') == '6') {
                                $query->where('transaction_type', $request->get('transaction'));
                            }
                            if ($request->get('debit_credit') == '0' || $request->get('debit_credit') == '1') {
                                $query->where('dr_cr', $request->get('debit_credit'));

                            }
                            if ($request->has('search')) {
                                $serchTerms = $request->search;
                                $query->where(function ($q) use ($serchTerms) {
                                    $q->where('username', 'like', "%{$serchTerms}%")
                                        ->orWhere('transaction_amount', 'like', "%{$serchTerms}%")
                                        ->orWhere('transaction_type', 'like', "%{$serchTerms}%")
                                        ->orWhere('game_name', 'like', "%{$serchTerms}%")
                                        ->orWhere('phone_no', 'like', "%{$serchTerms}%");
                                });
                            }
                        })
                        ->make(true);
                    // return DataTables::of(TransactionHistory::where('order_id','=','')->where('battle_id','!=','')->orderBy('id','DESC'))
                    // ->addIndexColumn()
                    // ->addColumn('view_user', function($row){

                    //     $actionBtn = '<a class="text-primary" href="battle_transactions/' . $row->user_id . '">'.$row->username.'</a>';
                    //     return $actionBtn;
                    // })
                    // ->filter(function ($instance) use ($request) {
                    // if ($request->get('transaction') == '3' || $request->get('transaction') == '4') {
                    //     $instance->where('transaction_type', $request->get('transaction'));

                    // }
                    // if ($request->get('debit_credit') == '0' || $request->get('debit_credit') == '1') {
                    //     $instance->where('dr_cr', $request->get('debit_credit'));

                    // }
                    //     if (!empty($request->get('search'))) {
                    //          $instance->where(function($w) use($request){
                    //             $search = $request->get('search');
                    //             $w->orWhere('username', 'LIKE', "%$search%")
                    //             ->orWhere('transaction_amount', 'LIKE', "%$search%")
                    //             ->orWhere('transaction_type', 'LIKE', "%$search%")
                    //             ->orWhere('game_name', 'LIKE', "%$search%");
                    //         });
                    //     }
                    // })
                    // ->rawColumns(['view_user'])
                    break;
                case 'user_battle_transaction':
                    return DataTables::of(TransactionHistory::orderBy('created_at', 'DESC')->where('user_id', $request->get('user_id'))->where('order_id', '=', '')->where('battle_id', '!=', ''))
                        ->make(true);
                    break;

                case 'wallet_transaction':
                    $walletQuery = datatables()
                        ->of(TransactionHistory::select(
                            [
                                'username',
                                'user_id',
                                'transaction_amount',
                                'transaction_type',
                                'dr_cr',
                                'order_id',
                                'closing_balance',
                                'game_name',
                                'battle_id',
                                'opposition_player',
                                'transaction_histories.created_at as date',
                                'u.phone_no as phone_no',
                            ])
                                ->leftJoin('users AS u', 'transaction_histories.user_id', '=', 'u.id')->where('transaction_histories.battle_id', '=', '')->where('transaction_histories.order_id', '!=', '')->where('transaction_histories.transaction_type', '!=', '5')->orderBy('date', 'DESC'));
                    return $walletQuery
                        ->filter(function ($query) use ($request) {
                            if ($request->get('transaction') == '1' || $request->get('transaction') == '2' || $request->get('transaction') == '5') {
                                $query->where('transaction_type', $request->get('transaction'));
                            }
                            if ($request->get('debit_credit') == '0' || $request->get('debit_credit') == '1') {
                                $query->where('dr_cr', $request->get('debit_credit'));

                            }
                            if ($request->has('search')) {
                                $serchTerms = $request->search;
                                $query->where(function ($q) use ($serchTerms) {
                                    $q->where('username', 'like', "%{$serchTerms}%")
                                        ->orWhere('transaction_amount', 'like', "%{$serchTerms}%")
                                        ->orWhere('transaction_type', 'like', "%{$serchTerms}%")
                                        ->orWhere('game_name', 'like', "%{$serchTerms}%")
                                        ->orWhere('phone_no', 'like', "%{$serchTerms}%");
                                });
                            }
                        })
                        ->make(true);
                    // return DataTables::of(TransactionHistory::where('order_id','!=','')->where('battle_id','=','')->orderBy('id','DESC'))
                    // ->addIndexColumn()
                    // ->addColumn('view_user', function($row){

                    //     $actionBtn = '<a class="text-primary" href="wallet_transactions/' . $row->user_id . '">'.$row->username.'</a>';
                    //     return $actionBtn;
                    // })
                    // ->filter(function ($instance) use ($request) {
                    //     if ($request->get('transaction') == '1' || $request->get('transaction') == '2' || $request->get('transaction') == '5' ) {
                    //         $instance->where('transaction_type', $request->get('transaction'));

                    //     }
                    //     if ($request->get('debit_credit') == '0' || $request->get('debit_credit') == '1') {
                    //         $instance->where('dr_cr', $request->get('debit_credit'));

                    //     }
                    //     if (!empty($request->get('search'))) {
                    //          $instance->where(function($w) use($request){
                    //             $search = $request->get('search');
                    //             $w->orWhere('username', 'LIKE', "%$search%")
                    //             ->orWhere('transaction_amount', 'LIKE', "%$search%")
                    //             ->orWhere('transaction_type', 'LIKE', "%$search%")
                    //             ->orWhere('game_name', 'LIKE', "%$search%");
                    //         });
                    //     }
                    // })
                    // ->rawColumns(['view_user'])
                    //     ->make(true);
                    break;
                case 'user_wallet_transaction':
                    return DataTables::of(TransactionHistory::orderBy('created_at', 'DESC')->where('user_id', $request->get('user_id'))->where('order_id', '!=', '')->where('battle_id', '=', ''))
                        ->make(true);
                    break;

                case 'referral_transaction':
                    $referralQuery = datatables()
                        ->of(Referral::select(
                            [
                                'username',
                                'user_id',
                                'referral_amount',
                                'transaction_type',
                                'dr_cr',
                                'order_id',
                                'referral_closing_balance',
                                'referrals.created_at as date',
                                'u.phone_no as phone_no',
                            ])
                                ->leftJoin('users AS u', 'referrals.user_id', '=', 'u.id')->orderBy('date', 'DESC'));
                    return $referralQuery
                        ->filter(function ($query) use ($request) {
                            if ($request->get('transaction') == '1' || $request->get('transaction') == '2') {
                                $query->where('transaction_type', $request->get('transaction'));
                            }
                            if ($request->get('debit_credit') == '0' || $request->get('debit_credit') == '1') {
                                $query->where('dr_cr', $request->get('debit_credit'));

                            }
                            if ($request->has('search')) {
                                $serchTerms = $request->search;
                                $query->where(function ($q) use ($serchTerms) {
                                    $q->where('username', 'like', "%{$serchTerms}%")
                                        ->orWhere('referral_amount', 'like', "%{$serchTerms}%")
                                        ->orWhere('transaction_type', 'like', "%{$serchTerms}%")
                                        ->orWhere('phone_no', 'like', "%{$serchTerms}%");
                                });
                            }
                        })
                        ->make(true);
                    // return DataTables::of(Referral::orderBy('id','DESC'))
                    // ->addIndexColumn()
                    // ->addColumn('view_user', function($row){

                    //     $actionBtn = '<a class="text-primary" href="referral_transactions/' . $row->user_id . '">'.$row->username.'</a>';
                    //     return $actionBtn;
                    // })
                    // ->filter(function ($instance) use ($request) {
                    //     if ($request->get('transaction') == '1' || $request->get('transaction') == '2') {
                    //         $instance->where('transaction_type', $request->get('transaction'));

                    //     }
                    //     if ($request->get('debit_credit') == '0' || $request->get('debit_credit') == '1') {
                    //         $instance->where('dr_cr', $request->get('debit_credit'));

                    //     }
                    //     if (!empty($request->get('search'))) {
                    //          $instance->where(function($w) use($request){
                    //             $search = $request->get('search');
                    //             $w->orWhere('username', 'LIKE', "%$search%")
                    //             ->orWhere('referral_amount', 'LIKE', "%$search%")
                    //             ->orWhere('transaction_type', 'LIKE', "%$search%");
                    //         });
                    //     }
                    // })
                    // ->rawColumns(['view_user'])
                    //     ->make(true);
                    break;

                case 'user_referral_transaction':
                    return DataTables::of(Referral::orderBy('created_at', 'DESC')->where('user_id', $request->get('user_id')))
                        ->make(true);
                    break;

                case 'admin_commission_history':
                    return DataTables::of(AdminCommissionHistory::orderBy('created_at', 'DESC'))
                        ->make(true);
                    break;

                case 'penalty_transaction':
                    return DataTables::of(PenaltyHistory::orderBy('created_at', 'DESC'))
                        ->filter(function ($instance) use ($request) {
                            if ($request->get('penalties_type') == '1' || $request->get('penalties_type') == '2') {
                                $instance->where('penalty_type', $request->get('penalties_type'));

                            }
                            if (!empty($request->get('search'))) {
                                $instance->where(function ($w) use ($request) {
                                    $search = $request->get('search');
                                    $w->orWhere('username', 'LIKE', "%$search%")
                                        ->orWhere('mobile_no', 'LIKE', "%$search%")
                                        ->orWhere('penalty_amount', 'LIKE', "%$search%")
                                        ->orWhere('battle_id', 'LIKE', "%$search%");
                                });
                            }
                        })
                        ->make(true);
                    break;
                case 'user_penalty_transaction':
                    return DataTables::of(PenaltyHistory::orderBy('created_at', 'DESC')->where('user_id', $request->get('user_id')))
                        ->make(true);
                    break;

                case 'kyc':
                    return DataTables::of(User::all())
                        ->addIndexColumn()
                        ->addColumn('action', function ($row) {

                            $actionBtn = '<a class="btn btn-primary text-light" href="kycs/' . $row->id . '">View</a>';
                            return $actionBtn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
                    break;

                case 'gamelistings':
                    return DataTables::of(Gamelisting::orderBy('created_at','DESC')->get())
                        ->addIndexColumn()
                        ->addColumn('view', function ($row) {
                            $viewBattle = '<a class="btn btn-primary text-light" href="battles/' . $row->id . '">View Battles</a>
                        ';
                            return $viewBattle;
                        })
                        ->addColumn('action', function ($row) {
                            $actionBtn = '<a class="btn btn-info text-light" href="game_listings/' . $row->id . '/edit">Edit</a>
                        ';
                            return $actionBtn;
                        })
                        ->addColumn('action2', function ($row) {
                            return view('admin.game_listings.delete', compact('row'))->render();
                        })
                        ->rawColumns(['view', 'action', 'action2'])
                        ->make(true);
                    break;

                case 'battles':
                    // $data =  DataTables::of(Battle::with('gamelisting')->get());
                    // return ["data"=>$data];
                    $gameId = $request->get('gameId');
                    return DataTables::of(Battle::with('gamelisting')->where('game_listing_id', $gameId)->get())
                        ->addIndexColumn()
                        ->addColumn('edit', function ($row) use ($gameId) {

                            $editBtn = '<a class="btn btn-info text-light" href="/admin/battles/' . $row->id . '/edit/' . $gameId . '">Edit</a>
                            ';
                            return $editBtn;
                        })
                        ->addColumn('delete', function ($row) {
                            return view('admin.battles.delete', compact('row'))->render();
                        })
                        ->rawColumns(['edit', 'delete'])
                        ->make(true);
                    break;

                case 'user_profiles':
                    return DataTables::of(User::all())
                        ->addIndexColumn()
                        ->addColumn('action', function ($row) {

                            $actionBtn = '<a class="btn btn-primary text-light" href="user_profiles/' . $row->id . '">Update Profile</a>';
                            return $actionBtn;
                        })
                        ->rawColumns(['action'])
                        ->make(true);
                    break;

                case 'kycUpload':
                    // return DataTables::of(KycUpload::select('kyc_uploads.id as uid','document_number','first_name','last_name','dob','front_photo','back_photo','kyc_status','kyc_uploads.created_at')->with('user')->with('state')->with('document'))
                    $kycQuery = datatables()
                        ->of(KycUpload::select(
                            [
                                'kyc_uploads.id as kyc_id',
                                'document_number',
                                'first_name',
                                'last_name',
                                'dob',
                                'front_photo',
                                'back_photo',
                                'kyc_status',
                                'kyc_uploads.created_at as date',
                                'u.name as name',
                                'u.id as user_id',
                                's.state as state',
                                'd.document_type as document_type',
                            ])
                                ->leftJoin('users AS u', 'kyc_uploads.user_id', '=', 'u.id')
                                ->leftJoin('states AS s', 'kyc_uploads.state_id', '=', 's.id')
                                ->leftJoin('documents AS d', 'kyc_uploads.document_id', '=', 'd.id')
                                ->orderBy('date', 'DESC'));
                    return $kycQuery
                        ->addIndexColumn()
                        ->addColumn('kyc_date', function ($row) {
                            $kycDate = $row->created_at;
                            return $kycDate;
                        })
                        ->addColumn('dob-date', function ($row) {
                            $dob_date = date("d-m-Y", strtotime($row->dob));
                            return $dob_date;
                        })
                        ->addColumn('frontPhoto', function ($row) {

                            $front_photo = '<!-- Button trigger modal -->

                            <img onclick=setModal(this) width="60px" height="50px" data-toggle="modal" data-target="#exampleModalCenter" src="' . env('IMAGE_URL') . 'kyc-documents/' . $row->user_id . '/' . $row->front_photo . '" alt="' . $row->front_photo . '" class="imgResize"/>';
                            return $front_photo;
                        })
                        ->addColumn('backPhoto', function ($row) {

                            $back_photo = '<img onclick=setModal(this) width="60px" height="50px" data-toggle="modal" data-target="#exampleModalCenter" src="' . env('IMAGE_URL') . 'kyc-documents/' . $row->user_id . '/' . $row->back_photo . '" alt="' . $row->back_photo . '" class="imgResize"/>';
                            return $back_photo;
                        })
                        ->addColumn('editKyc', function ($row) {

                            // $editBtn = '<a class="btn btn-primary text-light" href="kyc_uploads/' . $row->user_id .'/edit'. '">Update Kyc</a>';
                            $editBtn = '<a class="btn btn-primary text-light" href="/admin/kyc_uploads/' . $row->user_id .'/edit">Update Kyc</a>';
                            return $editBtn;
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->get('status') == '0' || $request->get('status') == '1') {
                                $instance->where('kyc_status', $request->get('status'));

                            }
                            if (!empty($request->get('search'))) {
                                $instance->where(function ($w) use ($request) {
                                    $search = $request->get('search');
                                    $w->orWhere('document_number', 'LIKE', "%$search%")
                                        ->orWhere('first_name', 'LIKE', "%$search%")
                                        ->orWhere('last_name', 'LIKE', "%$search%")
                                        ->orWhere('dob', 'LIKE', "%$search%");
                                });
                            }
                        })
                        ->rawColumns(['dob-date', 'frontPhoto', 'backPhoto', 'editKyc', 'kyc_date'])
                        ->make(true);
                    break;

                case 'rooms':
                    // $data = $request->get('status');
                    //  return["data"=>$data];
                    return DataTables::of(Room::orderBy('created_at', 'DESC')->where('status', '!=', '2')->where('status', '!=', '4'))
                    // return DataTables::of( Room::leftjoin('gamelistings', 'gamelistings.id', '=','rooms.game_id' )->select('rooms.id','rooms.code','rooms.status','gamelistings.name')->orderBy('rooms.id','DESC')->get())
                        ->addIndexColumn()
                        ->addColumn('view', function ($row) {
                            // print_r($row);
                            $viewBattle = '<a class="btn btn-primary btn-xs text-light" href="gameDetail/' . $row->id . '">View Detail</a>
                                ';
                            return $viewBattle;
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->get('status') == '1' || $request->get('status') == '3') {
                                $instance->where('status', $request->get('status'));
                            }
                            if (!empty($request->get('search'))) {
                                $instance->where(function ($w) use ($request) {
                                    $search = $request->get('search');
                                    $w->orWhere('code', 'LIKE', "%$search%")
                                        ->orWhere('game_name', 'LIKE', "%$search%")
                                        ->orWhere('status', 'LIKE', "%$search%");
                                });
                            }
                        })
                        ->rawColumns(['view'])
                        ->make(true);
                    break;

                case 'room_history':
                    return DataTables::of(RoomHistory::all())
                        ->addIndexColumn()
                        ->addColumn('game_screenshot', function ($row) {
                            // dump("/storage/images/game_result/$row->id/$row->screenshot");
                            $screenshot = '<img width="40px" src=' . env('IMAGE_URL') . 'game_result/' . $row->player_id . '/' . $row->screenshot . '" alt="' . $row->screenshot . '"/>';
                            return $screenshot;
                        })
                        ->addColumn('updateStatus', function ($row) {

                            $editBtn = '<a class="btn btn-primary text-light" href="room_historys/' . $row->id . '/edit">Update Status</a>';
                            return $editBtn;
                        })
                        ->rawColumns(['game_screenshot', 'updateStatus'])
                        ->make(true);
                    break;

                case 'mannual_withdrawl_transaction':
                    // return DataTables::of(MannualTransaction::all())
                    $mannualTransaction = datatables()
                        ->of(MannualTransaction::select(
                            [
                                'id',
                                'user_id',
                                'name',
                                'phone_no',
                                'amount',
                                'status',
                                'transfer_type',
                                'upi_id',
                                'account_number',
                                'ifsc_code',
                                'created_at',
                            ])->orderBy('created_at','DESC')
                        );
                    return $mannualTransaction
                        ->addIndexColumn()
                        ->addColumn('editWithdraw', function ($row) {
                            $editBtn = '<a class="btn btn-primary text-light" href="/admin/mannual_withdrawls/' . $row->id . '">Update Status</a>';
                            return $editBtn;
                        })
                        ->filter(function ($instance) use ($request) {
                            if ($request->get('status') == '0' || $request->get('status') == '1' || $request->get('status') == '2') {
                                $instance->where('status', $request->get('status'));
                            }
                            if (!empty($request->get('search'))) {
                                $instance->where(function ($w) use ($request) {
                                    $search = $request->get('search');
                                    $w->orWhere('name', 'LIKE', "%$search%")
                                        ->orWhere('amount', 'LIKE', "%$search%")
                                        ->orWhere('phone_no', 'LIKE', "%$search%")
                                        ->orWhere('upi_id', 'LIKE', "%$search%")
                                        ->orWhere('ifsc_code', 'LIKE', "%$search%")
                                        ->orWhere('account_number', 'LIKE', "%$search%");
                                });
                            }
                        })
                        ->rawColumns(['editWithdraw'])
                        ->make(true);
                    break;

                case 'commissions':
                    $data = ReferCommission::orderBy('id', 'DESC')->first();
                    return ["data" => $data];
                    return DataTables::of(ReferCommission::orderBy('id', 'DESC')->first())
                        ->make(true);
                    break;

                default:
                    abort(500);
            }
        }
    }
}

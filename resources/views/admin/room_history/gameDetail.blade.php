@extends('layouts.admin')
@section('content')
@can('user_create')
@endcan

@if($gameDetails->isEmpty())
<h1>No Player Found</h1>
@elseif (count($gameDetails) < 2) <h2 style="text-transform: capitalize; font-weight:bold;">{{isset($gameDetails['0']->player_name) ? $gameDetails['0']->player_name : ''}}<span style="color: black; font-weight:100"> is joined the battle </span></h2>
    <h3 style="text-transform: capitalize;color: black; font-weight:100">Waiting For other player to join so battle get started</h3>
    @else

    <div class="card">
        <div class="card-header details">

            <h4>Game Detail</h4>
            @if(
            ($gameDetails['0']->player_shared_status =='0' && $gameDetails['1']->player_shared_status == '0')
            ||
            ($gameDetails['0']->player_shared_status =='1' && $gameDetails['1']->player_shared_status == '1')
            ||
            ($gameDetails['0']->player_shared_status =='2' && $gameDetails['1']->player_shared_status == '2')
            ||
            ($gameDetails['0']->player_shared_status =='0' || $gameDetails['1']->player_shared_status == '0')
            ||
            (($gameDetails['0']->player_shared_status == '3' || $gameDetails['1']->player_shared_status == '3') && ($gameDetails['0']->player_shared_status != '3' || $gameDetails['1']->player_shared_status != '3'))
            )
            <form action="{{ route("admin.gameDetail.cancel", [$gameDetails['0']->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="admin_provided_status" id="admin_provided_status" value="1" />
                <input type="hidden" name="room_id" id="room_id" value="{{$gameDetails['0']->room_id}}" />
                <input type="hidden" name="player_id" id="player_id" value="{{$gameDetails['0']->player_id}}" />
                <div>
                    <input class="btn btn-primary" type="submit" value="Cancel Battle">
                </div>
            </form>
            @endif

            @if(session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul class="p-0 m-0" style="list-style: none;">
                    <li>{{ session()->get('message') }}</li>
                </ul>
            </div>
            @endif
        </div>
        <div class="card-body inner-card-wrap">
            <div class="row">
                <div class="col-lg-3 offset-lg-3 col-md-4 offset-md-2">
                    <div class="card p-3 inner-card">
                        <!-- <img src="/storage/images/game_result/{{ $gameDetails['0']->player_id }}/{{ $gameDetails['0']->screenshot }}" alt=" {{ $gameDetails['0']->screenshot ? $gameDetails['0']->screenshot : ''   }}" width="200px" height="200px"> -->
                        <img onclick=setModal(this) data-toggle="modal" data-target="#exampleModalCenter" class="image" src="{{ $gameDetails['0']->screenshot =='notFound.png' ? env('IMAGE_URL').'notFound.png' : env('IMAGE_URL').'game_result/'.$gameDetails['0']->player_id.'/'.$gameDetails['0']->screenshot.''}}" alt="screenshot" height="200px" style="object-fit:cover" />
                        <div class="card-body">
                            <!-- <h5 class="card-title">Player-1- {{ $gameDetails['0']->player_name }}</h5> -->
                            <h5 class="card-title">Phone-No {{ $gameDetails['0']['user']->phone_no }}</h5>
                            @if($gameDetails['0']->penalty_status == '1')
                            <p class="text-success font-weight-bold">Penalty applied!</p>
                            @endif
                            <h6 class="mt-2">Game Name- {{ $gameDetails['0']->game_name }}</h6>
                            <h6 class="mt-2">Room Code- {{ $gameDetails['0']->room_code }}</h6>
                            <h6 class="mt-2">Winning Price- {{ $gameDetails['0']->price }}</h6>
                            <h6 class="mt-2">Entry Fees- {{ $gameDetails['0']->entry_fees }}</h6>
                            <h6 class="mt-2 {{ $gameDetails['0']->player_shared_status == '1' ? 'text-success' : 'text-danger' }} ">Battle Status- {{ ($gameDetails['0']->player_shared_status == '1' ? 'Win' : ($gameDetails['0']->player_shared_status == '3' ? 'Cancelled' : ($gameDetails['0']->player_shared_status =='2' ? 'Loss' : 'Pending')) ) }}</h6>

                            @if(
                            ($gameDetails['0']->player_shared_status =='1' && $gameDetails['1']->player_shared_status == '1')
                            ||
                            ($gameDetails['0']->player_shared_status =='2' && $gameDetails['1']->player_shared_status == '2')
                            ||
                            (($gameDetails['0']->player_shared_status == '3' || $gameDetails['1']->player_shared_status == '3') && ($gameDetails['0']->player_shared_status != '3' || $gameDetails['1']->player_shared_status != '3'))
                            ||
                            (($gameDetails['0']->player_shared_status == '0' || $gameDetails['1']->player_shared_status == '0') && ($gameDetails['0']->player_shared_status != '0' || $gameDetails['1']->player_shared_status != '0'))
                            )
                            <form action="{{ route("admin.gameDetail.update", [$gameDetails['0']->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="admin_provided_status" id="admin_provided_status" value="1" />
                                <input type="hidden" name="room_id" id="room_id" value="{{$gameDetails['0']->room_id}}" />
                                <input type="hidden" name="player_id" id="player_id" value="{{$gameDetails['0']->player_id}}" />
                                <div>
                                    <input class="btn btn-danger" type="submit" value="Select Winner">
                                </div>
                            </form>
                            @endif

                            @if(
                            ($gameDetails['0']->player_shared_status =='0' && $gameDetails['1']->player_shared_status == '0')
                            ||
                            ($gameDetails['0']->player_shared_status =='1' && $gameDetails['1']->player_shared_status == '1')
                            ||
                            ($gameDetails['0']->player_shared_status =='2' && $gameDetails['1']->player_shared_status == '2')
                            ||
                            ($gameDetails['0']->player_shared_status =='0' || $gameDetails['1']->player_shared_status == '0')
                            ||
                            (($gameDetails['0']->player_shared_status == '3' || $gameDetails['1']->player_shared_status == '3') && ($gameDetails['0']->player_shared_status != '3' || $gameDetails['1']->player_shared_status != '3'))
                            )
                            <form class="mt-4" action="{{ route("admin.gameDetail.penalty", [$gameDetails['0']->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="admin_provided_status" id="admin_provided_status" value="1" />
                                <input type="hidden" name="room_id" id="room_id" value="{{$gameDetails['0']->room_id}}" />
                                <input type="hidden" name="player_id" id="player_id" value="{{$gameDetails['0']->player_id}}" />
                                <div>
                                    @if($gameDetails['0']->penalty_status == '0')
                                    <input class="btn btn-warning" type="submit" value="Apply Penalty">
                                    @endif
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4">
                    <div class="card p-3 inner-card">
                        <!-- <img src="/storage/images/game_result/{{ $gameDetails['1']->player_id }}/{{ $gameDetails['1']->screenshot }}" height="200px" width="200px" alt="{{ $gameDetails['1']->screenshot   }}" width="100" height="200px" style="object-fit:cover"> -->
                        <img onclick=setModal(this) class="image" data-toggle="modal" data-target="#exampleModalCenter" src="{{ $gameDetails['1']->screenshot =='notFound.png' ? env('IMAGE_URL').'/notFound.png' : env('IMAGE_URL').'game_result/'.$gameDetails['1']->player_id.'/'.$gameDetails['1']->screenshot.''}}" alt="screenshot" height="200px" style="object-fit:cover" />
                        <div class="card-body">
                            <!-- <h5 class="card-title">Player-2- {{ $gameDetails['1']->player_name }}</h5> -->
                            <h5 class="card-title">Phone-No {{ $gameDetails['1']['user']->phone_no }}</h5>
                            @if($gameDetails['1']->penalty_status == '1')
                            <p class="text-success font-weight-bold">Penalty applied!</p>
                            @endif
                            <h6 class="mt-2">Game Name- {{ $gameDetails['1']->game_name }}</h6>
                            <h6 class="mt-2">Room Code- {{ $gameDetails['1']->room_code }}</h6>
                            <h6 class="mt-2">Winning Price- {{ $gameDetails['1']->price }}</h6>
                            <h6 class="mt-2">Entry Fees- {{ $gameDetails['1']->entry_fees }}</h6>
                            <h6 class="mt-2 {{ $gameDetails['1']->player_shared_status == '1' ? 'text-success' : 'text-danger' }} ">Battle Status- {{ ($gameDetails['1']->player_shared_status == '1' ? 'Win' : ($gameDetails['1']->player_shared_status == '3' ? 'Cancelled' : ($gameDetails['1']->player_shared_status =='2' ? 'Loss' : 'Pending')) ) }}</h6>

                            @if(
                            ($gameDetails['0']->player_shared_status =='1' && $gameDetails['1']->player_shared_status == '1')
                            ||
                            ($gameDetails['0']->player_shared_status =='2' && $gameDetails['1']->player_shared_status == '2')
                            ||
                            (($gameDetails['0']->player_shared_status == '3' || $gameDetails['1']->player_shared_status == '3') && ($gameDetails['0']->player_shared_status != '3' || $gameDetails['1']->player_shared_status != '3'))
                            ||
                            (($gameDetails['0']->player_shared_status == '0' || $gameDetails['1']->player_shared_status == '0') && ($gameDetails['0']->player_shared_status != '0' || $gameDetails['1']->player_shared_status != '0'))
                            )
                            <form action="{{ route("admin.gameDetail.update", [$gameDetails['1']->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="admin_provided_status" id="admin_provided_status" value="1" />
                                <input type="hidden" name="room_id" id="room_id" value="{{$gameDetails['1']->room_id}}" />
                                <input type="hidden" name="player_id" id="player_id" value="{{$gameDetails['1']->player_id}}" />

                                <div>
                                    <input class="btn btn-danger" type="submit" value="Select Winner">
                                </div>
                            </form>

                            @endif

                            @if(
                            ($gameDetails['0']->player_shared_status =='0' && $gameDetails['1']->player_shared_status == '0')
                            ||
                            ($gameDetails['0']->player_shared_status =='1' && $gameDetails['1']->player_shared_status == '1')
                            ||
                            ($gameDetails['0']->player_shared_status =='2' && $gameDetails['1']->player_shared_status == '2')
                            ||
                            ($gameDetails['0']->player_shared_status =='0' || $gameDetails['1']->player_shared_status == '0')
                            ||
                            (($gameDetails['0']->player_shared_status == '3' || $gameDetails['1']->player_shared_status == '3') && ($gameDetails['0']->player_shared_status != '3' || $gameDetails['1']->player_shared_status != '3'))
                            )
                            <form class="mt-4" action="{{ route("admin.gameDetail.penalty", [$gameDetails['1']->id]) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="admin_provided_status" id="admin_provided_status" value="1" />
                                <input type="hidden" name="room_id" id="room_id" value="{{$gameDetails['0']->room_id}}" />
                                <input type="hidden" name="player_id" id="player_id" value="{{$gameDetails['0']->player_id}}" />
                                <div>
                                @if($gameDetails['1']->penalty_status == '0')
                                    <input class="btn btn-warning" type="submit" value="Apply Penalty">
                                @endif
                                </div>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modal')
    @endif

    @endsection
    @section('scripts')
    @parent

    @endsection

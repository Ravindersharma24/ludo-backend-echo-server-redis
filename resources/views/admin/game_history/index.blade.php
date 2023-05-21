@extends('layouts.admin')
@section('content')
@can('user_create')
<div style="margin-bottom: 10px;" class="row">
</div>
@endcan
<div class="card">
    <div class="card-header">
        <h4>Game History</h4>
    </div>
    <div class="card-body">

    <form action="{{ route('admin.game_history.index') }}" method="GET" style="margin-top: 20px;" class="game-history-filter">
    @csrf
        <div class="form-group d-flex" style="width:30vw;">
            <select class="form-control w-50" name="game_id" id="input">
                <option value="0">All Game</option>
                @foreach ($game as $key=>$game_name)
                    <option value="{{ $game_name->id }}" {{ $game_name->id == $selected_id['game_id'] ? 'selected' : '' }}>
                    {{ $game_name->name }}
                    </option>
                @endforeach
            </select>
            <!-- <select class="form-control ml-2" name="entry_fees" id="entry_fees">
                <option value="0">Entry Fees</option>
                @foreach ($battle as $key=>$entry_fees)
                    <option value="{{ $entry_fees->entry_fees }}" {{ $entry_fees->entry_fees == $selected_entry_fees['entry_fees'] ? 'selected' : '' }}>
                    {{ $entry_fees->entry_fees }}
                    </option>
                @endforeach
            </select> -->
            <!-- <input type="text" class="form-control ml-2" placeholder="Search Player" name="player_name"/> -->
            <input type="submit" class="btn btn-danger btn-sm ml-4" value="Filter">
        </div>
    </form>
        <div>
            <table class="table table-bordered table-striped table-hover datatable" id="rooms_table">
                <thead>
                    <tr>
                        <th>
                           Game-Name
                        </th>
                        <th>
                            Room-Code
                        </th>
                        <th>
                            Entry-Fees
                        </th>
                        <th>
                            Winning-Price
                        </th>
                        <th>
                            Admin-Commission
                        </th>
                        <th>
                            Player-1
                        </th>
                        <th>
                            Player-2
                        </th>
                        <th>
                            Player-1-Status
                        </th>
                        <th>
                            Player-2-Status
                        </th>
                        <th>
                            Player-1-Screenshot
                        </th>
                        <th>
                            Player-2-Screenshot
                        </th>
                        <th>
                            Player-1-Cancel-Reason
                        </th>
                        <th>
                            Player-2-Cancel-Reason
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                @if($rooms->isEmpty())
                    <h1>No Records Found</h1>
                @else
                @foreach ($rooms as $key=>$data )
                <?php
$players = $data->player_name;
$player_name = explode(",", $players);

$player_status = $data->player_shared_status;
$player_status_arr = explode(",", $player_status);

$player_id = $data->player_id;
$player_id_arr = explode(",", $player_id);

$screenshot = $data->screenshot;
$player_screenshot = explode(",", $screenshot);

$cancel_note = $data->cancel_note;
$player_cancel_note = explode(",", $cancel_note);

$player1 = isset($player_name[0]) ? $player_name[0] : '';
$player2 = isset($player_name[1]) ? $player_name[1] : '';

$player1Status = isset($player_status_arr[0]) ? $player_status_arr[0] : '';
$player2Status = isset($player_status_arr[1]) ? $player_status_arr[1] : '';

$player1Img = isset($player_screenshot[0]) ? $player_screenshot[0] : '';
$player2Img = isset($player_screenshot[1]) ? $player_screenshot[1] : '';

$player1cancelNote = isset($player_cancel_note[0]) ? $player_cancel_note[0] : '';
$player2cancelNote = isset($player_cancel_note[1]) ? $player_cancel_note[1] : '';

$player1Id = isset($player_id_arr[0]) ? $player_id_arr[0] : '';
$player2Id = isset($player_id_arr[1]) ? $player_id_arr[1] : '';

?>
                <tr>
                    <td>{{$data->game_name}}</td>
                    <td>{{$data->room_code}}</td>
                    <td>{{$data->entry_fees}}</td>
                    <td>{{$data->price}}</td>
                    <td>{{$data->admin_commission}}</td>
                    <td>{{$player1}}</td>
                    <td>{{$player2}}</td>

                    <td style="color:{{ ($player1Status =='1' ? 'rgb(12, 173, 82)' : ($player1Status =='3' ? 'Blue' : 'Red')) }}">{{ ($player1Status =='1' ? 'Win' : ($player1Status =='3' ? 'Cancelled' : ($player1Status =='2' ? 'Loss' : 'Pending')))}}</td>
                    <td style="color:{{ ($player2Status =='1' ? 'rgb(12, 173, 82)' : ($player2Status =='3' ? 'Blue' : 'Red')) }}">{{ ($player2Status =='1' ? 'Win' : ($player2Status =='3' ? 'Cancelled' : ($player2Status =='2' ? 'Loss' : 'Pending')))}}</td>

                    <?php
$image = '';
if ($player1Img != 'notFound.png') {
    $image = env('IMAGE_URL') . 'game_result/' . $player1Id . '/' . $player1Img;
} else {
    $image = env('IMAGE_URL') . 'notFound.png';
}
?>
                    <td class="text-center">
                        @if($player1Img != null)
                        <img data-toggle="modal" data-target="#exampleModal" class="image" src="{{ $image }}" alt="" width="60px" height="50px"/>
                        <!-- <button type="button" class="btn btn-light" data-toggle="modal" data-target="#exampleModal">
                        </button> -->
                        @endif
                    </td>
                    <?php
$image = '';
if ($player2Img != 'notFound.png') {
    $image = env('IMAGE_URL') . 'game_result/' . $player2Id . '/' . $player2Img;
} else {
    $image = env('IMAGE_URL') . 'notFound.png';
}
?>
                    <td class="text-center">
                        @if($player2Img != null)
                        <img data-toggle="modal" data-target="#exampleModal" class="image" src="{{ $image }}" alt="" width="60px" height="50px"/>
                        <!-- <button type="button" class="btn btn-light" data-toggle="modal" data-target="#exampleModal">
                        </button> -->
                        @endif
                    </td>
                    <td>{{$player1cancelNote}}</td>
                    <td>{{$player2cancelNote}}</td>
                    <td>{{ $data->created_at }}</td>
                    <td><a class="btn btn-primary btn-xs text-light" href="gameDetail/{{$data->room_id}}">View Detail</a></td>
                </tr>
                @endforeach
                @endif

                </tbody>
            </table>
        </div>
        {{ $rooms->links() }}
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <img src="" width="100%" id="modalImage" />
      </div>
    </div>
  </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    // ====================== Showing Up of the Image
    $(document).on("click", ".image", function(e) {
            e.preventDefault();
            let image = $(e.target).attr("src");
            $("#modalImage").attr("src", image)
        });
</script>

<script>
    $(function () {
        $('#rooms_table').DataTable({
            // processing: true,
            responsive:true,
            paginate:false,
            bInfo: false,
            aaSorting: [],
        });
    });
</script>
@endsection

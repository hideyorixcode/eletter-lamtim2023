@if(count($listLog)>0)

    <div class="card card-hero">
        <div class="card-header">
            <div class="card-icon">
                <i class="far fa-question-circle"></i>
            </div>
            <h4>Cek</h4>
            <div class="card-description">Data Logs</div>
        </div>
        <div class="card-body p-0">
            @foreach($listLog as $dataLogs)
                <div class="tickets-list">
                    <a href="#" class="ticket-item">
                        <div class="ticket-title">
                            <h4>{{$dataLogs->log_Description}}</h4>
                        </div>
                        <div class="ticket-info">
                            <div>{{Auth::user()->name}}</div>
                            <div class="bullet"></div>
                            <div class="text-primary">{{TanggalIndowaktu($dataLogs->log_Time)}}</div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{ $listLog->links() }}
@else
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <div class="alert-body">
            TIDAK DITEMUKAN DATA
        </div>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
@endif


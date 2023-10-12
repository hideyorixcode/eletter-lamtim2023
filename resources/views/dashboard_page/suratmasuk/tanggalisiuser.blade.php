<div class="activity">
    <div class="activity-icon bg-{{$status}} text-white shadow-{{$status}}">
        <i class="{{$icon}}"></i>
    </div>
    <div class="activity-detail" style="width: 100%">
        <div class="mb-2">
                                                                       <span
                                                                           class="text-job text-primary">{{TanggalIndowaktu($data->tgl_diterima)}}</span>
        </div>

        <ul class="list-group">

            @if($data->status=='diteruskan')
                <li class="list-group-item">Diterima oleh :
                    <strong>{{cek_opd($data->penerima)->nama_opd}}</strong> -
                    a.n. {{$data->nama_penerima}}</li>
                @php
                    $kepada = $data->kepada;

                @endphp
                @if($kepada!='')
                    @php $arr_kepada = explode (",", $kepada); @endphp
                    <li class="list-group-item">Diteruskan kepada : <br/>
                        @foreach($arr_kepada as $x)
                            - <strong>{{cek_opd($x)->nama_opd}}</strong><br/>
                        @endforeach
                    </li>
                @endif
                @if($data->melalui_id_opd)
                    <li class="list-group-item">Melalui :
                        <strong>{{cek_opd($data->melalui_id_opd)->nama_opd}}</strong>
                    </li>
                @endif
                @if($data->catatan_disposisi)
                    <li class="list-group-item">Catatan
                        : {{$data->catatan_disposisi}}</li>
                @endif
            @else
                <li class="list-group-item">Diolah oleh :
                    <strong>{{cek_opd($data->penerima)->nama_opd}}</strong> -
                    a.n. {{$data->nama_penerima}}</li>
            @endif

        </ul>


    </div>
</div>


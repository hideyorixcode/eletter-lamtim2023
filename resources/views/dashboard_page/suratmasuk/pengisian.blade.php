<div class="activity">
    <div class="activity-icon bg-{{$status}} text-white shadow-{{$status}}">
        <i class="{{$icon}}"></i>
    </div>
    <div class="activity-detail" style="width:100%!important; overflow: auto">
        <input type="hidden" id="id_{{$no}}" name="id_{{$no}}" value="{{$data->id}}">
        <input type="hidden" id="mode_{{$no}}" name="mode_{{$no}}" value="tambah">
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Tanggal Diterima</label>
                    @if($input_tgl!=null)
                        <input class="datetimepickerindo form-control"
                               name="tgl_diterima_{{$no}}"
                               id="tgl_diterima_{{$no}}" required
                               type="text"
                               value="{{$input_tgl}}"/>
                    @else
                        <input class="datetimepickerindokosong form-control"
                               name="tgl_diterima_{{$no}}"
                               id="tgl_diterima_{{$no}}"
                               {{$data->status=='diteruskan' ? 'required' : ''}}
                               type="text"
                               value="{{$input_tgl}}" autocomplete="off"/>
                        <button onclick="set_tanggal({{$no}})"
                                class="btn btn-sm btn-outline-primary mt-2"
                                type="button">
                            Set
                            Tgl
                            dan
                            Waktu
                        </button>

                        <button onclick="reset_tanggal({{$no}})"
                                class="btn btn-sm btn-outline-danger mt-2"
                                type="button">
                            Reset
                            Tanggal
                        </button>
                    @endif
                    <div class="invalid-feedback"
                         id="error_tgl_diterima">
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Diterima oleh</label>
                    <input type="hidden" id="penerima_{{$no}}"
                           name="penerima_{{$no}}" class="form-control"
                           readonly value="{{$data->penerima}}">
                    <input type="text" id="opd_penerima_{{$no}}"
                           name="opd_penerima_{{$no}}"
                           class="form-control"
                           readonly value="{{cek_opd($data->penerima)->nama_opd}}">

                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Nama Penerima</label>
                    <input class="form-control"
                           name="nama_penerima_{{$no}}"
                           id="nama_penerima_{{$no}}"
                           value="{{$data->nama_penerima ? $data->nama_penerima : Auth::user()->name}}"
                           required
                           placeholder="Nama Penerima">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Status</label>
                    <select id="status_{{$no}}"
                            name="status_{{$no}}"
                            onchange="check_status({{$no}})"
                            class="form-control" required>
                        @if(cekJenisOPD($data->penerima)!='opd')
                            <option
                                value="diteruskan" {{$data->status=='diteruskan' ? 'selected' : ''}}>
                                DITERUSKAN
                            </option>
                        @endif
                        <option
                            value="diolah" {{$data->status=='diolah' ? 'selected' : ''}}>
                            DIOLAH
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-sm-4" id="div_kepada_{{$no}}"  @if($data->status=='diolah') style="display: none" @endif>
                <div class="form-group">
                    <label>Kepada</label>

                    <select id="kepada_{{$no}}"
                            name="kepada_{{$no}}[]"
                            class="form-control select_cari"
                            multiple {{$data->status=='diteruskan' ? 'required' : ''}}>
                        @foreach($listPerangkat as $nama => $value)
                            <option
                                value="{{$value}}" {{array_search($value, explode (",", $data->kepada)) !== false ? 'selected' : ''}}>{{$nama}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-sm-4" id="div_melalui_{{$no}}"   @if($data->status=='diolah') style="display: none" @endif>
                <div class="form-group">
                    <label>Melalui/Langsung</label>
                    <select id="melalui_id_opd_{{$no}}"
                            name="melalui_id_opd_{{$no}}"
                            class="form-control select_cari"
                            @if($data->status=='diteruskan') required @endif>

                        @foreach($listTu as $nama => $value)
                            <option
                                value="{{$value}}" {{$value==$data->melalui_id_opd ? 'selected' : ''}}>{{$nama}}</option>
                        @endforeach
                        <option
                            value="" {{$data->melalui_id_opd==null ? 'selected' : ''}}>
                            Langsung Tanpa Perantara
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row" id="div_catatan_{{$no}}" @if($data->status=='diolah') style="display: none" @endif>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Catatan</label>



                    <textarea class="form-control"
                              style="min-height: 100px;"
                              rows="4"
                              placeholder="Ketik Catatan Disposisi..."
                              name="catatan_disposisi_{{$no}}"
                              id="catatan_disposisi_{{$no}}">{{$data->catatan_disposisi}}</textarea>

                </div>
            </div>
        </div>
    </div>
</div>

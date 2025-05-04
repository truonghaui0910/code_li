<div class="modal fade " id="dialog_cookie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="card-box m-b-0 pa-15">
                <div class="modal-header pa-5">
                    <button type="button" class="close-custom" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                    <h4 class="modal-title"><span class="dialog-icon"><i class="fa fa-plus"></i></span> <span>Add Cookie</span></h4>                        
                </div>
                <div id="dialog_cookie_loading" style="text-align: center;display: none"><i class="fa fa-spinner fa-spin"></i> Loading...</div>
                <div class="pa-10 modal-body ">
                    <div class="content-log">
                        <p>Bước 1: Login tiktok tại <a target="_blank" href="https://shop.tiktok.com/streamer/live">đây</a> </p>
                        <p>Bước 2: Xem hướng dẫn lấy cookie bằng extension Cookie-Editor <a target="_blank" href="https://blog.autolive.vip/huong-dan-livestream-tiktok-us/">đây</a> </p>
                        <p>Bước 3: Nhập Cookie vào ô text rồi ấn Lưu Lại </p>
                        <form id="form-cookie">
                            {{ csrf_field() }}
                            <input id="profile_cookie_id" type="hidden" name="profile_cookie_id"/>
                            <label for="infinite_loop">Cookie</label>
                            <textarea id="cookie_content" class="form-control resize-ta" name="cookie_content" rows="8" spellcheck="false"></textarea>
                        </form>
                        <button type="button" class="btn btn-dark waves-effect waves-light btn-save-cookie m-t-15"
                                data-toggle="tooltip" data-placement="top"
                                title="Lưu lại"><i class="fa fa-save cur-point"></i> Lưu lại</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
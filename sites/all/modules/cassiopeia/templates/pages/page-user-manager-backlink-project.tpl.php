<?php drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/user-backlink.js', ['weight' => 1000]); ?>
<div class="page page-backlink">
    <div class="page-header">
        <div class="page-title">
            <h1>Tất cả dự án từ khoá</h1>
            <button type="button" class="btn-green btn-add-project">Thêm mới dự án</button>
        </div>

        <div class="page-search">
            <form action="#">
                <div class="input-group-search">
                    <input name="search-key" type="text" placeholder="Tìm kiếm...">
                    <button type="button" class="btn-type-1 btn-submit btn-search-project">Tìm từ khoá</button>
                </div>
            </form>
        </div>
    </div>

    <div class="page-utilities">
        <div class="page-utilities-left">
            <form action="#">
                <select name="" id="" class="btn-gray btn-type-1">
                    <option value="">Bulk Action</option>
                    <option value="">item 1</option>
                    <option value="">item 2</option>
                </select>
                <button type="submit" class="btn-submit btn-type-1">Áp dụng</button>
            </form>
        </div>
        <div class="page-utilities-right">
            <form action="#">
                <button type="submit" class="btn-df btn-green">
                    Xuất báo cáo
                    <span class="icon material-icons">south</span>
                </button>
            </form>
        </div>
    </div>

    <div class="page-container">
        <div class="page-main">
            <div class="result">

            </div>
        </div>
    </div>

</div>

<!-- Modal BackLink -->
<div class="modal fade Modal-addNew backlink-addNew" id="backlinkModal" tabindex="-1" role="dialog" aria-labelledby="backlinkModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-content-contaienr">
                <form action="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Tạo dự án mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body-content">
                            <h3>Nhập Trang Web Của Bạn</h3>
                            <span>LinkAssistant sẽ giúp bạn tìm cơ hội đặt backlink cho trang web dựa trên chủ đề của nó và đối thủ cạnh tranh SEO</span>
                            <div class="website-name">
                                <input type="text" placeholder="Nhập tên website...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="modal-footer-content">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Huỷ</button>
                            <button type="submit" class="btn btn-success">Tạo mới</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

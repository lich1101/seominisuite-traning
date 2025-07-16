<?php
// Chỉ xóa session khi người dùng bắt đầu tìm kiếm mới
// Không xóa ngay khi vào trang để người dùng có thể xuất Excel
if (isset($_GET['reset']) || !isset($_SESSION['GetUrlData'])) {
    $_SESSION['GetUrlData'] = null;
}
drupal_add_js(drupal_get_path('module', 'cassiopeia') . '/js/get-url.js', ['weight' => 1000]);
?>
<div class="page-get-url">
    <span id="listOfID" data-value=""></span>
    <input hidden type="number" id="totalItems">
    <input hidden type="number" id="checkedItems">
    <div class="page-container">

        <?php if (!empty($_SESSION['GetUrlData'])): ?>
            <div class="alert alert-info">
                <strong>Thông báo:</strong> Bạn đã có dữ liệu từ lần tìm kiếm trước.
                <a href="/get-url/result" class="btn btn-sm btn-info">Xem kết quả</a>
                <a href="/get-url?reset=1" class="btn btn-sm btn-warning">Tìm kiếm mới</a>
            </div>
        <?php endif; ?>

        <form action="" id="form-get-url">

            <div class="form-group">
                <label for="">Từ khóa</label>
                <textarea class="form-control" name="keywords" id="" cols="30" rows="10" placeholder="Mỗi từ khóa 1 dòng..."></textarea>
            </div>
            <div class="form-group">
                <label for="">Loại trừ urls</label>
                <textarea class="form-control" name="exclude-urls" id="" cols="30" rows="10" placeholder=""></textarea>
            </div>

            <div class="form-group">
                <input type="checkbox" name="include">&nbsp;
                <label for="">Bao gồm urls</label>
                <textarea class="form-control" name="include-urls" id="" cols="30" rows="10" placeholder=""></textarea>
            </div>
            <div class="form-group">
                <label for="">Số kết quả</label>
                <input type="number" max="100" class="form-control" value="100" name="max_results">
            </div>
          <div class="form-group form-item-captcha-resolve">
            <select name="captcha-resolve" id=""
                    class="btn form-control btn-gray btn-type-1">
              <option value="auto" selected>Giải Captcha tự động</option>
              <option value="manual" >Giải Captcha thủ công</option>
            </select>
          </div>
            <div class="form-group">
                <button class="btn btn-primary" type="button">Xác nhận</button>
            </div>
        </form>
    </div>
    <div class="progress-block">
        <div class="block-container">
<!--            <div class="close">&times;</div>-->
            <div class="block-title">Đang thu thập dữ liệu...</div>
            <div class="progress-bar-block active modal-custom">
                <progress id="file" value="0" max="2180"> 32% </progress>
            </div>
        </div>
    </div>
</div>


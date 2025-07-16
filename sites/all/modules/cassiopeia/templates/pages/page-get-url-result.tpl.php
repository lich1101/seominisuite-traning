<?php
//$_SESSION['GetUrlData'] = null;
//_print_r($_SESSION['GetUrlData']);
$index=1;
$current = null;
$current_key = isset($_REQUEST['keyword'])?$_REQUEST['keyword']:"";
if(!empty($_SESSION['GetUrlData'])){
    if(isset($_REQUEST['keyword'])){
        $current = !empty($_SESSION['GetUrlData'][$_REQUEST['keyword']])?!empty($_REQUEST['keyword'])?$_SESSION['GetUrlData'][$_REQUEST['keyword']]:null:null;
    }
}
$items = !empty($_SESSION['GetUrlData'])?$_SESSION['GetUrlData']:array();
$nodes = [];
$exclude = [];
if(isset($_REQUEST['keyword'])&&$_REQUEST['keyword']!="all"){
    $nodes = !empty($_SESSION['GetUrlData'][$_REQUEST['keyword']])?$_SESSION['GetUrlData'][$_REQUEST['keyword']]:array();
    // Lấy danh sách loại trừ theo key
    if (!empty($_SESSION['GetUrlExclude'][$_REQUEST['keyword']])) {
        $exclude = preg_split('/\r?\n/', $_SESSION['GetUrlExclude'][$_REQUEST['keyword']]);
        $exclude = array_map('trim', $exclude);
        $exclude = array_filter($exclude);
    }
}else{
//    _print_r($items);
    if(!empty($items)){
        foreach($items as $item){
            if(!empty($item)){
                foreach($item as $_key => $_item){
//                    _print_r($_key);
                    $nodes[$_key] = $_item;
                }
            }
        }
    }
}
// Loại trừ các URL chứa chuỗi trong danh sách loại trừ
if (!empty($exclude) && !empty($nodes)) {
    foreach ($nodes as $url => $value) {
        foreach ($exclude as $ex_url) {
            if ($ex_url !== '' && strpos($url, $ex_url) !== false) {
                unset($nodes[$url]);
                break;
            }
        }
    }
}
//_print_r($nodes);
?>
<div class="page-get-url-response">
    <div class="page-container">
        <div class="mb-15">
            <a href="/get-url" class="btn btn-default">Quay lại</a>
        </div>
        <form action="">
            <div class="form-group">
                <label for="">Từ khóa:</label>
                <select name="keyword" id="" class="form-control" onchange="this.form.submit();">
                    <option value="all">Tất cả</option>
                    <?php if(!empty($_SESSION['GetUrlData'])): ?>
                        <?php foreach($_SESSION['GetUrlData'] as $key => $value): ?>
                            <option <?php if(isset($_REQUEST['keyword'])&&$key==$_REQUEST['keyword']) echo "selected"; ?> value="<?php echo htmlspecialchars($key); ?>"><?php echo ($key); ?></option>
                            <?php $index++; endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <?php if(isset($current_key)): ?>
                    <a class="btn btn-success" href="/get-url/result?keyword=<?php echo $current_key ?>&export=1">Xuất Excel</a>
                <?php endif; ?>
            </div>
        </form>
        <div class="result">
            <div class="block-title">
                <b>Có <?php echo count($nodes); ?> kết quả được tìm thấy</b>
            </div>
            <table class="table table-hovered table-stripped">
                <thead>
                <tr>
                    <th>URL</th>
                    <th width="100px">Số lượng ký tự</th>
                    <th>Anchor Text</th>
                </tr>
                </thead>
                <tbody>
                <?php if(!empty($items)): ?>
                    <?php foreach($nodes as $_key => $value): ?>
                        <tr>
                            <td class=""><?php echo $_key; ?></td>
                            <td><?php echo strlen($_key); ?></td>
                            <td><?php echo $value; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

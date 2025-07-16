<?php
global $user;
$query = db_select("cassiopeia_content_check","cassiopeia_content_check");
$query->fields("cassiopeia_content_check");
$query->condition("uid",$user->uid);
$result = $query->execute()->fetchAll();
$min_words = 0;
$max_words = 0;
$h2_count = 0;
$h3_count = 0;
$img_count = 0;
if(!empty($result)){
    _print_r($result);
}
?>
<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#home">Đánh giá bài viết</a></li>
    <li><a data-toggle="tab" href="#menu1">Hỗ trợ dàn ý</a></li>
    <li><a data-toggle="tab" href="#menu2">Chi tiết đối thủ</a></li>
</ul>

<div class="tab-content">
    <div id="home" class="tab-pane fade in active tab-1">
        <div class="tab-title">Điểm đánh giá</div>
        <div class="tab-body">
            <div class="tab-note">
                Seo Minisuite đưa ra  9 tiêu đề ( H1), 28 Heading ( H2) và 41 Heading (H3). Kết quả lấy từ 10 đối thủ có thứ hạng cao nhất.
            </div>
            <div class="tab-chart">

            </div>
            <div class="tab-detail">
                <table class="table-hover table-stripped">
                    <tbody>
                    <tr>
                        <td>
                            <div>860</div>
                            <div>Số từ hiện tại</div>
                        </td>
                        <td>
                            <div>2.355 - 3.422</div>
                            <div>Số từ tiêu chuẩn</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>5</div>
                            <div>Số H2 hiện tại</div>
                        </td>
                        <td>
                            <div>4-6</div>
                            <div>Số H2 tiêu chuẩn</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>9</div>
                            <div>Số H3 hiện tại</div>
                        </td>
                        <td>
                            <div>10 - 13</div>
                            <div>Số H3 tiêu chuẩn</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div>0</div>
                            <div>Số hình ảnh hiện tại</div>
                        </td>
                        <td>
                            <div>5 - 12</div>
                            <div>Hình ảnh tiêu chuẩn</div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="menu1" class="tab-pane fade tab-2">
        <div class="tab-title">Hỗ trợ dàn ý bài viết</div>
        <div class="tab-body">
            <div class="tab-note">
                Seo Minisuite đưa ra  9 tiêu đề ( H1), 28 Heading ( H2) và 41 Heading (H3). Kết quả lấy từ 10 đối thủ có thứ hạng cao nhất.
            </div>
            <div class="tab-detail">
                <div class="">
                    <ul>
                        <li class="expanded active header">
                            <a href="#" class="sm-event-none">
                                <div>#</div>
                                <div>Title</div>
                                <div>Thứ hạng</div>
                                <div><i class="fa fa-angle-up"></i></div>
                            </a>
                            <ul class="mb-none">
                                <li>
                                    <div>H1</div>
                                    <div>Dịch vụ thiết kế website chuyên nghiệpMona Media Mona Media chuyên nghiệpMona Me...</div>
                                    <div>1</div>
                                    <div><button class="btn-add-heading"><i class="fa fa-plus"></i> Thêm</button></div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div id="menu2" class="tab-pane fade tab-2">
        <div class="tab-title">Chi tiết đối thủ</div>
        <div class="tab-body">
            <!--                                    <div class="tab-note">-->
            <!--                                        Seo Minisuite đưa ra  9 tiêu đề ( H1), 28 Heading ( H2) và 41 Heading (H3). Kết quả lấy từ 10 đối thủ có thứ hạng cao nhất.-->
            <!--                                    </div>-->
            <div class="tab-detail">
                <div class="">
                    <ul>
                        <li class="expanded active header">
                            <a href="#" class="sm-event-none">
                                <div>
                                    <div class="article-title">Thiết Kế Website Chuyên Nghiệp, Trọn Gói - Mona Media</div>
                                    <i class="fa fa-angle-up"></i>
                                </div>
                                <div>
                                    Thiết Kế Website Chuyên Nghiệp, Trọn Gói - Mona Media
                                </div>
                            </a>
                            <ul class="mb-none">
                                <li>
                                    <div>H1</div>
                                    <div>Dịch vụ thiết kế website chuyên nghiệpMona Media Mona Media chuyên nghiệpMona Me...</div>
                                    <div>1</div>
                                    <div><button class="btn-add-heading"><i class="fa fa-plus"></i> Thêm</button></div>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
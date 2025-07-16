
<?php
function cassiopeia_momo_api_capture_wallet(){
    $partnerCode    = "MOMOHMDV20210315";
    $partnerName    = "MOMOHMDV20210315";
    $storeId        = "MOMOHMDV20210315";
    $requestType    = "captureWallet";
    $ipnUrl         = 'https://seominisuite.com/momo-ipn-response';
    $redirectUrl    = 'https://seominisuite.com/momo-payment-response';
    $orderId        = "T0002";
    $amount         = "100000";
    $lang           = 'vi';
    $orderInfo      = "100000";
    $requestId      = "MM02";
    $extraData      = "eyJ1c2VybmFtZSI6ICJtb21vIn0=";


    $accessKey      = "oJQGqenOxDm5fNpL";
    $data = "accessKey=".$accessKey."&amount=".$amount."&extraData=".$extraData."&ipnUrl=".$ipnUrl."&orderId=".$orderId."&orderInfo=".$orderInfo."&partnerCode=".$partnerCode."&redirectUrl=".$redirectUrl."&requestId=".$requestId."&requestType=".$requestType;
    $secret_key = "Es55GHLkwknYyxlRpveSeB2C9S9ejug1";
    $signature      = hash_hmac('sha256', $data,$secret_key);

    $curl = curl_init();
    $datas = new stdClass();
    $datas->partnerCode     = $partnerCode;
    $datas->partnerName     = $partnerName;
    $datas->storeId         = $storeId;
    $datas->requestType     = $requestType;
    $datas->ipnUrl          = $ipnUrl;
    $datas->redirectUrl     = $redirectUrl;
    $datas->orderId         = $orderId;
    $datas->amount          = $amount;
    $datas->lang            = $lang;
    $datas->orderInfo       = $orderInfo;
    $datas->requestId       = $requestId;
    $datas->extraData       = $extraData;
    $datas->signature       = $signature;

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://test-payment.momo.vn/v2/gateway/api/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>json_encode($datas),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    print_r(json_encode($datas));
    $response = curl_exec($curl);

    curl_close($curl);
    _print_r(($response));
}
cassiopeia_momo_api_capture_wallet();
$google_map = variable_get("contact_map");
$contact_title = variable_get("contact_title");
//    $lienhe
$lienhe = variable_get('contact_content', array(
    'value' => '',
    'format' => 'full_html'
));
?>
<div id="contact-page">
    <div class="page-inner">
        <div class="container contact-page-container">
            <div class="page-title">
                <h1>Liên hệ</h1>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <!-- map-container-->
                    <div class="map-container">
                        <div class="maps" id="map_canvas">
                            <?php if(!empty($google_map)){
                                print($google_map);
                            } ?>
                        </div>
                    </div>
                    <!-- e: map-container-->
                </div>
                <div class="col-lg-6">
                    <!-- contact-container-->
                    <div class="contact-container">
                        <?php if(!empty($lienhe)){
                            print($lienhe['value']);
                        } ?>
                        <div class="contact-form">
                            <p>
                                <strong>
                                    <?php if(!empty($contact_title)){
                                        print($contact_title);
                                    } ?>
                                </strong>
                            </p>
                            <?php
                            $contact_form = drupal_get_form("cassiopeia_contact_form");
                            if(!empty($contact_form)){
                                $contact_form = drupal_render($contact_form);
                                print($contact_form);
                            }
                            ?>
                        </div>
                    </div>
                    <!-- e: contact-container-->
                </div>
            </div>
        </div>
    </div>
</div>

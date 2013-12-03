<?php
/*
FILL OUT THIS INFORMATION
************************/
$api_key = 'ENTER YOUR API KEY HERE FROM https://www.litecoinpool.org/account'; //API from litecoinpool
$litecoinaddy= 'YOUR LITECOIN ADDRESS';//litecoin address
$work1='USERNAME.1';//worker1 name
$work2='USERNAME.2';//worker2 name
/***********************************/

function get_data($url)
{
    $ch = curl_init();
    $timeout = 0;
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}



$url="https://www.litecoinpool.org/api?api_key=$api_key";
$get_received = file("http://explorer.litecoin.net/chain/Litecoin/q/getreceivedbyaddress/$litecoinaddy");
$get_sent = file("http://explorer.litecoin.net/chain/Litecoin/q/getsentbyaddress/$litecoinaddy");



$litecoinpool = json_decode(get_data($url)); 
     
$response = array();
$hashrate = $litecoinpool->user->hash_rate;
$totalrewards = $litecoinpool->user->total_rewards;
$paid = $litecoinpool->user->paid_rewards;
$unpaid = $litecoinpool->user->unpaid_rewards;
$past24 = $litecoinpool->user->past_24h_rewards;

$worker1 = $litecoinpool->workers->$work1->hash_rate;
$worker2 = $litecoinpool->workers->$work2->hash_rate;
$price = $litecoinpool->market->ltc_usd;
$xvert = $litecoinpool->market->ltc_btc;
$btc = $litecoinpool->market->btc_usd;
$past24Total = $past24 * $price;
$pastTotal = $paid * $price;
$diffnext = $litecoinpool->network->retarget_time;
$diffnext_time = ($diffnext/60)/60;
$currdiff = $litecoinpool->network->difficulty;
$nextdiff = $litecoinpool->network->next_difficulty;
$change = (($nextdiff / $currdiff) *100) - 100;
$received = array_shift($get_received);
$sent = array_shift($get_sent);
$received_value = explode(' ', $received);
$sent_value = explode(' ', $sent);
$wallet_value = $received_value[0] - $sent_value[0];
$wallet_cash = $wallet_value * $price;
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Miner</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<link rel="apple-touch-icon-precomposed" href="images/apple-touch-icon-precomposed.png"/>
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />

  <link href="css/codiqa.ext.min.css" rel="stylesheet">
  <link href="css/jquery.mobile-1.3.1.min.css" rel="stylesheet">

  <script src="js/jquery-1.9.1.min.js"></script>
  <script src="js/jquery.mobile-1.3.1.min.js"></script>
  <script src="js/codiqa.ext.min.js"></script>
  <script src="js/miner.js"></script>
  <script src="js/custom.js"></script>
</head>
<body>
  <div data-role="page" data-control-title="Home" data-theme="a" id="page1">
      <div data-role="panel" id="panel1" data-position="left" data-display="reveal"
      data-theme="a">
          <ul data-role="listview" data-divider-theme="h" data-inset="false">
              <li data-role="list-divider" role="heading">
                  Divider
              </li>
              <li data-theme="a">
                  <a href="" data-transition="slide">
                      Button
                  </a>
              </li>
          </ul>
      </div>
      <div data-theme="a" data-role="header" data-position="fixed">
          <a data-role="button" data-theme="b" onclick="refreshPage()">
              Refresh
          </a>
          <a data-role="button" data-theme="e" href="#"><?php echo "$".number_format($price,2);?></a>
          <h3>
              <?php echo number_format($wallet_value,2)." ltc"; ?>
          </h3>
          
      </div>
      <div data-role="content">
          <div style=" text-align:center" data-controltype="image">
              <font size="18px"><?php echo number_format($hashrate)."</font> kh/s";?>
          </div>
          <div data-role="collapsible-set" data-theme="a">
              <div data-role="collapsible" data-collapsed="false">
                  <h3>
                      Miner Data
                  </h3>
                  <h3>Worker 1 : <?php echo " ".number_format($worker1)." kh/s";?></h3>
                  <h3>Worker 2 : <?php echo " ".number_format($worker2)." kh/s";?></h3>
              </div>
          </div>
        
           <div data-role="collapsible-set" data-theme="a">
              <div data-role="collapsible" data-collapsed="true">
                  <h3>Network</h3>
                  <h3>Next Difficulty: <?php echo number_format($diffnext_time,0)." Hours";?></h3>
                  <h3>Change: 
                  <?php 
                      if ($nextdiff > $currdiff)
                        {
                          echo " +".number_format($change,0)."%";
                        }
                      else
                        {
                          echo " -".number_format($change,0)."%";
                        }
                      
                      ?>
                  </h3>
                  
              </div>
          </div>
          
          <div data-role="collapsible-set" data-theme="a">
              <div data-role="collapsible" data-collapsed="true">
                  <h3>Financial Data</h3>
                  <h3>Mined : <?php echo $paid." ltc";?></h3>
                  <h3>Pending : <?php echo number_format($unpaid,4)." ltc";?></h3>
                  <h3>Mined Profit: <?php echo "$".number_format($pastTotal,2);?></h3>
                  <h3>24 hr Mined: <?php echo number_format($past24,2);?></h3>
                  <h3>24 hr Profit: <?php echo "$".number_format($past24Total,2);?></h3>
                  <h3>LTC / BTC : <?php echo number_format(($xvert*1000),0);?> ltc = 1 BTC</h3>
                  <h3>BTC / USD : <?php echo "$".number_format($btc,2);?></h3>
              </div>
          </div>
          <div data-role="collapsible-set" data-theme="a">
              <div data-role="collapsible" data-collapsed="true">
                  <h3>Wallet</h3>
                  <div style=" text-align:center" data-controltype="image"><img style="width: 90%; height: 90%" src="images/qr.png"></div>
                  <p></br></p>
                  <p></br></p>
                  <h3 style=" text-align:center"><?php echo "$".number_format($wallet_cash,2);?></h3>
                  
              </div>
          </div>
          <div style="" data-controltype="image"><img style="width: 100%; height: px" src="images/litecoin_logo_medium.png"></div>
      </div>
      
  </div>
</body>
</html>

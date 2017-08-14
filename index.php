<?php

//set API key
$api_key = 'YourIDXBrokerAPIKey';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.idxbroker.com/clients/featured",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
  CURLOPT_HTTPHEADER => array(
    'accesskey: '.$api_key,
    'apiversion: 1.4.0',
    'cache-control: no-cache',
    'content-type: application/x-www-form-urlencoded',
    'outputtype: json',
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
  echo "cURL Error #:" . $err;
} else {

  //get the account domain ONLY if using custom subdomain
  $listings = json_decode($response, true);

  foreach($listings as $k => $l) {
  $link = $l[fullDetailsURL];
  $titelProtocol = explode("/", $link);
  $titleRoot = explode(".", $link);
  $titleExtention = explode("/", $titleRoot[2]);

  $titleLink =  $titelProtocol[0].'://'.$titleRoot[1].'.'.$titleExtention[0];

  break;
}

  // Send the headers for XML
  header('Content-type: text/xml');
  header('Pragma: public');
  header('Cache-control: private');
  header('Expires: -1');
  echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

  echo '<listings>
      <title>'.$titleLink.' Feed</title>
      <link rel="self" href="'.$titleLink.'"/>';

//create listings from API call
foreach ($listings as $key => $value) {

  if($value[idxPropType] = 'Single Family' || $value[address] !== ''){
    echo '<listing>
            <home_listing_id>'.$value[listingID].'</home_listing_id>
            <name>'.$value[address].'</name>
            <availability>for_sale</availability>
            <description>'.addslashes(htmlentities($value[remarksConcat])).'</description>
            <address format="simple">
                <component name="addr1">'.$value[streetNumber].' '.$value[streetDirection].' '.$value[streetName].'</component>
                <component name="city">'.$value[cityName].'</component>
                <component name="region">'.$value[state].'</component>
                <component name="country">United States</component>
                <component name="postal_code">'.$value[zipcode].'</component>
            </address>
            <latitude>'.$value[latitude].'</latitude>
            <longitude>'.$value[longitude].'</longitude>
            <image>
                <url>'.$value[image][0][url].'</url>
            </image>
            <listing_type>for_sale_by_agent</listing_type>
            <num_baths>'.$value[totalBaths].'</num_baths>
            <num_beds>'.$value[bedrooms].'</num_beds>
            <num_units>1</num_units>
            <price>'.$value[price].' USD</price>
            <property_type>house</property_type>
            <url>'.$value[fullDetailsURL].'</url>
        </listing>';
    }
}

  echo '</listings>';

}

 ?>

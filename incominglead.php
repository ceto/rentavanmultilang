<?php
    define( 'WP_USE_THEMES', FALSE );
    require( '../wp-load.php' );
?>

<?php
    $to_email = get_field('r_email', 'option');
    $bcc_email = get_field('bcc_email', 'option');

    // $to_email = "szabogabor@hydrogene.hu";
    // $bcc_email = "leads@vieeye.hu";

    $incomingsubject = __('VIARENT.HU LAKOSSÁGI | Ajánlatkérés','viarent');
    $respsubject = __('Köszönjük ajánlatkérésedet, megkaptuk, hamarosan jelentkezünk - VIARENT.', 'viarent');

    $data = array(
        'fname' => array (
            'label' => __('Keresztnév', 'viarent'),
            'value' => '',
        ),
        'lname' => array (
            'label' => __('Vezetéknév', 'viarent'),
            'value' => '',
        ),
        'email' => array (
            'label' => __('E-mail cím', 'viarent'),
            'value' => '',
        ),
        'tel' => array (
            'label' => __('Telefon', 'viarent'),
            'value' => '',
        ),
        'countrycode' => array (
            'label' => __('Ország kód', 'viarent'),
            'value' => '',
        ),
        'city' => array (
            'label' => __('Település', 'viarent'),
            'value' => '',
        ),
        'zip' => array (
            'label' => __('Irányítószám', 'viarent'),
            'value' => '',
        ),
        'address' => array (
            'label' => __('Utca, házszám', 'viarent'),
            'value' => '',
        ),
        'acceptgdpr' => array (
            'label' => __('Adatkezelés elfogadva', 'viarent'),
            'value' => '',
        ),
        'acceptmarketing' => array (
            'label' => __('Marketing célú felhasználás elfogadva', 'viarent'),
            'value' => '',
        ),
        'message' => array (
            'label' => __('Üzenet', 'viarent'),
            'value' => '',
        ),
        'vehicle' => array (
            'label' => __('Jármű', 'viarent'),
            'value' => '',
        ),
        'time' => array (
            'label' => __('Bérlés időtartama', 'viarent'),
            'value' => '',
        ),
        'audiencesource' => array (
            'label' => __('Honnan hallottál rólunk?', 'viarent'),
            'value' => '',
        ),
        'utm' => array (
            'label' => __('UTM', 'viarent'),
            'value' => '',
        )
    );
?>

<?php if($_POST) : ?>
<?php
    if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        $output = json_encode(array('type'=>'error', 'text' => 'Request must come from Ajax'));
        die($output);
    }

    if( (strlen($_POST["fname"]) < 2) || (strlen($_POST["lname"]) < 2) || (strlen($_POST["email"]) < 2) || (strlen($_POST["tel"]) < 2) || (strlen($_POST["address"]) < 2) || (strlen($_POST["zip"]) < 2) || (strlen($_POST["city"]) < 2) ) {
        $output = json_encode(array('type'=>'error', 'text' => __('Hiányzó kötelező mező. Ellenőrizze a megadott adatokat.','viarent') ));
        die($output);
    }

    $data['fname']['value'] = filter_var($_POST["fname"], FILTER_SANITIZE_STRING);
    $data['lname']['value'] = filter_var($_POST["lname"], FILTER_SANITIZE_STRING);
    $data['email']['value'] = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $data['tel']['value'] = filter_var($_POST["tel"], FILTER_SANITIZE_STRING);
    $data['countrycode']['value'] = strtoupper(filter_var($_POST["countrycode"], FILTER_SANITIZE_STRING));
    $data['address']['value'] = filter_var($_POST["address"], FILTER_SANITIZE_STRING);
    $data['city']['value'] = filter_var($_POST["city"], FILTER_SANITIZE_STRING);
    $data['zip']['value'] = filter_var($_POST["zip"], FILTER_SANITIZE_STRING);
    $data['vehicle']['value'] = filter_var($_POST["vehicle"], FILTER_SANITIZE_STRING);
    $data['time']['value'] = filter_var($_POST["time"], FILTER_SANITIZE_STRING);
    $data['acceptgdpr']['value'] = filter_var($_POST["acceptgdpr"], FILTER_SANITIZE_STRING);
    $data['acceptmarketing']['value'] = filter_var($_POST["acceptmarketing"], FILTER_SANITIZE_STRING);
    $data['audiencesource']['value'] = $theaudiencesources[filter_var($_POST["audiencesource"], FILTER_SANITIZE_STRING)];
    $audiencesourceid = explode('_', filter_var($_POST["audiencesource"], FILTER_SANITIZE_STRING))[0];

    $data['message']['value'] = filter_var($_POST["message"], FILTER_SANITIZE_STRING);
    $data['message']['value'] = str_replace("\&#39;", "'", $data['message']['value']);
    $data['message']['value'] = str_replace("&#39;", "'", $data['message']['value']);

    $data['utm']['value'] = filter_var(viarent_textify_array($_POST["utm"]), FILTER_SANITIZE_STRING);

    $sap_audiencesource = filter_var($_POST["sap_audiencesource"], FILTER_SANITIZE_STRING);
    $sap_VehicleCategory = filter_var($_POST["sap_VehicleCategory"], FILTER_SANITIZE_STRING);
    $sap_VehicleNature = filter_var($_POST["sap_VehicleNature"], FILTER_SANITIZE_STRING);

    if ((strlen($data['fname']['value'])<3) || (strlen($data['lname']['value'])<3)) {
        $output = json_encode(array('type'=>'error', 'text' => __('Teljes név megadása kötelező!','viarent') ));
        die($output);
    }

    if(strlen($data['tel']['value'])<6) {
        $output = json_encode(array('type'=>'error', 'text' => __('Érvénytelen telefonszám!','viarent') ));
        die($output);
    }

    if(!filter_var($data['email']['value'], FILTER_VALIDATE_EMAIL)) {
        $output = json_encode(array('type'=>'error', 'text' => __('Érvénytelen e-mail cím!','viarent') ));
        die($output);
    }

    if(!filter_var($data['acceptgdpr']['value'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE)) {
        $output = json_encode(array('type'=>'error', 'text' => __('Az adatkezelés elfogadása kötelező','viarent') ));
        die($output);
    }



    $metas = array();
    foreach ($data as $key => $dataitem) {
        if ($dataitem['value']!=='') {
            $metas[$key] = $dataitem['value'];
        }
    }
?>

<?php ob_start(); ?>
<table width="100%" cellpadding="5" cellspacing="0">
<?php foreach ($metas as $key => $datavalue) : ?>
    <tr>
        <td><strong><?= $data[$key]['label'] ?></strong></td>
        <td><?= $data[$key]['value'] ?></td>
    </tr>
<?php endforeach; ?>
</table>
<br><hr><br>
<?php $incominghtmlcontent = ob_get_clean(); ?>

<?php
    $thelead = array(
        'post_title'    => $data['name']['value'],
        'post_status'   => 'publish',
        'post_type'     => 'lead',
        'meta_input'    => $metas,
        'post_content'  => $incominghtmlcontent
    );

    if( $leadid = wp_insert_post( $thelead ) ) {
        wp_update_post( array(
            'ID' => $leadid,
            'post_title' => __('Rövidtávú ajánlatkérés', 'viarent').' / '.$data['name']['value'].' #'.$leadid
        ));
    }
?>

<?php
    if ($sapsyncisactive) {
        $home_url = parse_url(esc_url(home_url('/')));
        $domain = $home_url['host'];
        $sapdata = [
            'Name' => __('Retail rental quote', 'via').' ['.$domain.']',
            'IndividualCustomerGivenName' =>  $data['fname']['value'],
            'IndividualCustomerFamilyName' => $data['lname']['value'],
            'IndividualCustomerEMail' => $data['email']['value'],
            'IndividualCustomerPhone' => $data['tel']['value'],
            'Note' => $data['vehicle']['value']."\r\n".$data['time']['value']."\r\n\n".$data['message']['value'],
            'IndividualCustomerAddressCountry' => $data['countrycode']['value'],
            'IndividualCustomerAddressCity' => $data['city']['value'],
            'IndividualCustomerAddressStreetName' => $data['address']['value'],
            'IndividualCustomerAddressPostalCode' => $data['zip']['value'],
            'NatureofInterest_KUT' => '131', //Delta Truck - LAKOSSÁGI
            'LeadSource_KUT' => '121',
            'WhichCompany_KUT' => '111', //Viarent
            'LeadSocialSource_KUT' => $audiencesourceid,
            'Vehiclewebpage_KUT' => substr($_SERVER['HTTP_REFERER'],0,39),
            'VehicleCategory_KUT' =>  $sap_VehicleCategory,
            'VehicleNature_KUT' =>  $sap_VehicleNature,
            'IndividualCustomerContactAllowedCode' => ($data['acceptmarketing']['value']==1)?'1':'2',
            'utm_source_KUT' => filter_var($_POST['utm']['source'], FILTER_SANITIZE_STRING),
            'utm_medium_KUT' => filter_var($_POST['utm']['medium'], FILTER_SANITIZE_STRING),
            'utm_campaign_KUT' => filter_var($_POST['utm']['campaign'], FILTER_SANITIZE_STRING),
            'utm_term_KUT' => filter_var($_POST['utm']['term'], FILTER_SANITIZE_STRING),
            'utm_content_KUT' => filter_var($_POST['utm']['content'], FILTER_SANITIZE_STRING)  
        ];

        require_once(get_stylesheet_directory().'/lib/zipper.php');
        if ( $SAPStateCode = getSAPStateCode($data['countrycode']['value'], $data['zip']['value']) ) {
            $sapdata['IndividualCustomerAddressState'] = $SAPStateCode;
        }

        try {
            $saplead =  $odataClient->post('LeadCollection', $sapdata);
            $output = json_encode(array(
                'type'=> 'success',
                'text' => __('SAP lead added successfully!','gls'),
                'data' => $saplead
            ));
            $incominghtmlcontent.=__('SAP lead added successfully!','gls').'<br><hr><br>';
        } catch (Exception $e) {
            $output = json_encode(array(
                'type'=>'error',
                'text' => __('SAP sync error occured!','gls'),
                'data' => $e->getMessage()
            ));
            $incominghtmlcontent.=$e->getMessage().'<br><hr><br>';
        }

        /* debug die */
        // die($output);
    }
?>

<?php

    $incomingheaders = array(
        'From: '.$to_email,
        'Reply-To: '.$data['email']['value'],
        'BCC: '.$bcc_email,
        'X-Mailer: PHP/' . phpversion(),
        'Content-Type: text/html; charset=UTF-8'
    );

    $respincomingheaders = array(
        'From: '.$to_email,
        'Reply-To: '.$to_email,
        'BCC: '.$bcc_email,
        'X-Mailer: PHP/' . phpversion(),
        'Content-Type: text/html; charset=UTF-8'
    );

    $sentMail = @wp_mail($to_email, $incomingsubject, $incominghtmlcontent, $incomingheaders);

    if(!$sentMail) {
        $output = json_encode(array('type'=>'error', 'text' => __('Hiba történt küldés során, próbálkozz újra!','viarent')));
        die($output);
    } else {
        $incominghtmlcontent = get_field('emailthanks', 'option').$incominghtmlcontent;
        $respsentMail = @wp_mail($data['email']['value'], $respsubject, $incominghtmlcontent, $respincomingheaders);
        $output = json_encode(array('type'=>'success', 'text' => __('Köszönjük megkeresését! Üzenetét rögzítettük, munkatársunk hamarosan jelentkezik.','viarent')));
        die($output);
    }
?>
<?php endif; ?>
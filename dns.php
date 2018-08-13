<?php
//require_once "db_connect.php";
$dbhost = 'localhost';
$dbuser = 'domain';
$dbpass = 'Semiotics123!';
$dbname = 'rgs_domain';

$list = $_POST['domain'];
$dig = shell_exec("dig a $list +short");
$a = preg_split("#[\r\n]+#", $dig);
$apop = array_pop($a);
$dig1 = shell_exec("dig mx $list +short");
$mx = preg_split("#[\r\n]+#", $dig1);
$mxpop = array_pop($mx);
$dig2 = shell_exec("dig txt $list +short");
$txt = preg_split("#[\r\n]+#", $dig2);
$txtpop = array_pop($txt);
$dig3 = shell_exec("dig ns $list +short");
$ns = preg_split("#[\r\n]+#", $dig3);
$nspop = array_pop($ns);

  include 'header.php';
 ?>

<body>





  <!-- Page Content -->

  <div class="container-fluid">
    <div class="col-lg-offset-3">
      <h1>DOMAIN LOOKUP</h1>
    </div>
    <div class="row">
      <br/>
      <div class="col-lg-offset-1 col-lg-2">
      </div>
      <div class="col-lg-3">
        <form method="post" action="dns.php">
          <input class="form-control" type="domain" name="domain" placeholder="example.co.nz">
        </div>
        <div class="col-lg-3">
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>
    </div>

    <br/>
    <div class="row">
      <!-- Who Is goes here -->
      <div class="col-lg-7">
        <h1 class="page-header">WHOIS</h1>
        <?php
        $whois = shell_exec("whois $list");
        echo "<pre>" . $whois . "</pre>";
        // 2. Perform database query

        $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        $whois = mysqli_real_escape_string($conn, $whois);
        $list = mysqli_real_escape_string($conn, $list);
        $lookupdate = date("d/m/Y h:i:sa");
        $sql = "INSERT INTO whois (domain_name, whois_data, lookup_date) VALUES ('$list', '$whois', '$lookupdate')";
        //$sql = "INSERT INTO whois (domain_name, whois_data) VALUES ('$list', '$whois')";


        if (mysqli_query($conn, $sql)) {
            echo "Record updated successfully";
        } else {
            echo "Error inserting record: " . mysqli_error($conn);
        }
        ?>

      </div>
      <div class="col-lg-5">
        <h1 class="page-header">DNS</h1>
        <?php
        echo '<table class="table table-bordered table-hover table-condensed table-striped">
        <thead>
        <tr>
        <th>Domain</th>
        <th>Type</th>
        <th>Output</th>
        </tr>
        </thead>
        <tbody>';
        foreach ($a as $aItem){
          $rdnsAItem = shell_exec("dig -x $aItem +short");
          echo "<tr><td>" . $list ."</td><td style='background-color: #D9534F; color: #FFF;'>A</td><td>" . $aItem . ' <span style="color: #a6a6a6;"><em>[ ' . $rdnsAItem . " ]</em></span></td></tr>";
        }

        foreach ($mx as $mxItem){
          $rdnsMxItem = shell_exec("dig -x $mxItem +short");
          echo "<tr><td>" . $list ."</td><td style='background-color: #5CB85C; color: #FFF;'>MX</td><td>" . $mxItem . ' <span style="color: #a6a6a6;"><em>[ ' . $rdnsMxItem . " ]</em></span></td></tr>";
        }

        foreach ($txt as $txtItem){
          echo "<tr><td>" . $list ."</td><td style='background-color: #337AB7; color: #FFF;'>TXT</td><td>" . $txtItem . "</td></tr>";
        }

        foreach ($ns as $nsItem){
          echo "<tr><td>" . $list ."</td><td style='background-color: #F0AD4E; color: #FFF;'>NS</td><td>" . $nsItem . "</td></tr>";
        }

        echo "<tbody></table>";

        ?>
      </div>
    </div>
  </div>


  <?php
  // 5. Close database connection
  mysqli_close($connection);
  ?>
  <!-- jQuery -->


  <!-- Bootstrap Core JavaScript -->
  <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>


</body>

</html>

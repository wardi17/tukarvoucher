
<?php
$pages = isset($data['pages']) ? $data['pages'] : '';

?>
<div id="main">
       <header class="mb-3">
                <a href="#" class="burger-btn d-block d-xl-none">
                    <i class="bi bi-justify fs-3"></i>
                </a>
            </header>
    <!-- Content Header (Page header) -->
    <div class ="col-md-12 col-12">

      <!-- Default box -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Hello  </h3>
          <hr>
        </div>
        <div class="card-body">
          Selamat datang  <?=$pages?>
        </div>
        <!-- /.card-body -->
  
      <!-- /.content -->
  </div>
</div>
</div>



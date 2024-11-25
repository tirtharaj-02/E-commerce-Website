<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

	<?php include 'includes/navbar.php'; ?>
	 
	  <div class="content-wrapper">
	    <div class="container">

	      <!-- Main content -->
	      <section class="content">
	        <div class="row">
	        	<div class="col-sm-9">
	        		<?php
	        			if(isset($_SESSION['error'])){
	        				echo "
	        					<div class='alert alert-danger'>
	        						".$_SESSION['error']."
	        					</div>
	        				";
	        				unset($_SESSION['error']);
	        			}
	        		?>
					

	        		<center>
		                  <div class="item active">
		                    <img src="images/banner1.png" width=150% alt="First slide">
		                  </div>

		              </center>
		            
	        	</div>
	        	
	        </div>
	      </section>
	     
	    </div>
	  </div>
  
  	
</div>

<?php include 'includes/scripts.php'; ?>
</body>
</html>
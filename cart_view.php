<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<html>
<head>
</head>
<body class="hold-transition skin-blue layout-top-nav">
<div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
     
    <div class="content-wrapper">
        <div class="container">

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-sm-9">
                        <h1 class="page-header">SHOWING YOUR CART</h1>
                        <div class="box box-solid">
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>##</th>
                                            <th>Photo</th>
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th width="20%">Quantity</th>
                                            <th>Subtotal</th>
                                          
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                        <!-- Table rows will be dynamically populated here -->
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                        <td colspan="8" style="text-align: left;">
                                <button id="payButton" class="btn btn-danger" >Pay</button>

                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <?php
                            if(isset($_SESSION['user'])){
                                echo "
                                    <div id='Esewa-button'></div>
                                ";
                            }
                            else{
                                echo "
                                    <h4>You need to <a href='login.php'>Login</a> to checkout.</h4>
                                ";
                            }
                        ?>
                    </div>
                </div>
            </section>
         
        </div>
    </div>
    <?php $pdo->close(); ?>
    
</div>

<?php include 'includes/scripts.php'; ?>

<script>
var total = 0;
var usd_to_npr_rate = 123.45; // Replace with your actual conversion rate

$(function(){
    $(document).on('click', '.cart_delete', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        $.ajax({
            type: 'POST',
            url: 'cart_delete.php',
            data: {id:id},
            dataType: 'json',
            success: function(response){
                if(!response.error){
                    getDetails();
                    getCart();
                    getTotal();
                }
            }
        });
    });

    $(document).on('click', '.minus', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var qty = $('#qty_'+id).val();
        if(qty>1){
            qty--;
        }
        $('#qty_'+id).val(qty);
        $.ajax({
            type: 'POST',
            url: 'cart_update.php',
            data: {
                id: id,
                qty: qty,
            },
            dataType: 'json',
            success: function(response){
                if(!response.error){
                    getDetails();
                    getCart();
                    getTotal();
                }
            }
        });
    });

    $(document).on('click', '.add', function(e){
        e.preventDefault();
        var id = $(this).data('id');
        var qty = $('#qty_'+id).val();
        qty++;
        $('#qty_'+id).val(qty);
        $.ajax({
            type: 'POST',
            url: 'cart_update.php',
            data: {
                id: id,
                qty: qty,
            },
            dataType: 'json',
            success: function(response){
                if(!response.error){
                    getDetails();
                    getCart();
                    getTotal();
                }
            }
        });
    });

    getDetails();
    getTotal();

});

function getDetails(){
    $.ajax({
        type: 'POST',
        url: 'cart_details.php',
        dataType: 'json',
        success: function(response){
            $('#tbody').html(response);
            getCart();
        }
    });
}

function getTotal(){
    $.ajax({
        type: 'POST',
        url: 'cart_total.php',
        dataType: 'json',
        success:function(response){
            total = response;
            var totalNpr = convertToNpr(total); // Convert total to NPR
            updateTotalDisplay(totalNpr); // Update the display with converted total
        }
    });
}

function convertToNpr(amountUsd) {
    return amountUsd * usd_to_npr_rate;
}

function updateTotalDisplay(totalNpr) {
    $('#totalAmount').text('Total: रू ' + totalNpr.toFixed(2)); // Assuming there's an element with id totalAmount to display the total
}

$(document).ready(function() {
    $(document).on('click', '#payButton', function(e) {
        e.preventDefault();
        window.location.href = '/Ecommerce-Site/Esewa/indexesewa2.php';
    });
});
</script>

</body>
</html>

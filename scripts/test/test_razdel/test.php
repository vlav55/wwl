<!DOCTYPE html>
<html>
<head>
  <title>Выбор раздела</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <h2>Поиск городов</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="city">Город:</label>
                        <input type="text" class="form-control" id="city" name="city" autocomplete="off">
                    </div>
                    <div id="cityList"></div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#city').keyup(function(){
                var msgs_city = $(this).val();
                if(msgs_city != ''){
                    $.ajax({
                        url:"jquery_city.php",
                        method:"POST",
                        data:{msgs_city:msgs_city},
                        success:function(data){
                            $('#cityList').fadeIn();
                            $('#cityList').html(data);
                        }
                    });
                }
            });
            $(document).on('click', 'li', function(){
                $('#city').val($(this).text());
                $('#cityList').fadeOut();
            });
        });
    </script>
</body>
</html>

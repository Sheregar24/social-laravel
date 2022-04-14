<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Payment</title>

        <!-- Add stylesheet -->
        <link href="{{asset('css/style.css')}}" rel="stylesheet">
    </head>
    <body>
        <div class="container login-container">
            <div class="login-card">
                <div class="login-card-body">
                    <form action="{{url('/payment-initiate-request')}}" method="POST">
                        <div class="row">
                            <input type="hidden" value="{{csrf_token()}}" name="_token" />
                            <label for="name">Name</label> :
                            <input type="text" class="form-control" id="name"  name="name">
                        </div></br>
                        <div class="row">
                            <label for="email">Email</label> :
                            <input type="text" class="form-control" id="email" name="email">
                        </div></br>
                        <div class="row">
                            <label for="contactNumber">Contact Number</label> :
                            <input type="text" class="form-control" id="contactNumber" name="contactNumber">
                        </div></br>
                        <div class="row">
                            <label for="address">Address</label> :
                            <input type="text" class="form-control" id="address" name="address">
                        </div></br>
                        <div class="row">
                            <label for="amount">Amount</label> :
                            <input type="text" class="form-control" id="amount" name="amount">
                        </div></br>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

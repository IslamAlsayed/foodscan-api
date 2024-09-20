<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    @if (isset($success) && $success)
    <div class="alert alert-success">{{ $success }}</div>
    @endif

    @if (isset($danger) && $danger)
    <div class="alert alert-danger">{{ $danger }}</div>
    @endif

    <form action="{{ route('checkout_post') }}" method="POST">
        @csrf
        <input type="hidden" name="employee_id" value="1">
        <input type="hidden" name="customer_id" value="1">
        <input type="hidden" name="dining_table_id" value="1">
        <button class="btn btn-info m-5">Test Checkout Paymob</button>
    </form>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <title>{{ ucfirst($order->order_status) }} order</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            font-family: Arial, sans-serif;
            margin: 0 auto;
            padding: 20px;
            max-width: 600px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
        }

        .header {
            color: white;
            padding: 10px 0;
            text-align: center;
        }

        .header.in_progress {
            background-color: #3548dc;
        }

        .header.cancelled {
            background-color: #e12929;
        }

        .header.done {
            background-color: #4CAF50;
        }

        h2 {
            margin: 0;
        }

        .content {
            padding: 20px;
        }

        .content p {
            margin: 10px 0;
        }

        .content ul {
            list-style-type: none;
            padding: 0;
        }

        .content ul li {
            background-color: #efefef;
            margin: 5px 0;
            padding: 10px;
            border-radius: 4px;
        }

        .content ul li b.in_progress {
            color: #3548dc;
        }

        .content ul li b.cancelled {
            color: #e12929;
        }

        .content ul li b.done {
            color: #4CAF50;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .button.in_progress {
            background-color: #3548dc;
        }

        .button.cancelled {
            background-color: #e12929;
        }

        .button.done {
            background-color: #4CAF50;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header {{ $order->order_status }}">
            <h2>
                @if($order->order_status === 'in_progress')
                Order In Progress
                @elseif($order->order_status === 'cancelled')
                Order Cancelled
                @elseif($order->order_status === 'done')
                Order Completed
                @endif
            </h2>
        </div>
        <div class="content">
            <p>Dear {{ ucfirst($order->customer->name) }},</p>
            <p>{{ $statusMessage }}</p>
            <p>Order Details:</p>
            <ul>
                <li>Order Number: {{ $order->id }}</li>
                <li>Total Amount: ${{ $order->total }}</li>
                <li>Order Status:
                    <b class="{{ $order->order_status }}">
                        @if($order->order_status === 'in_progress')
                        In Progress
                        @elseif($order->order_status === 'cancelled')
                        Cancelled
                        @elseif($order->order_status === 'done')
                        Completed
                        @endif
                    </b>
                </li>
            </ul>
            <a href="{{ url('/api/orders/show/' . $order->id) }}" class="button {{ $order->order_status }}">View Order</a>
            <p>Thank you for choosing us!</p>
            <p>Casher: {{ ucfirst($order->employee->name) }}</p>
            <p>Best regards, <b>foodScan</b></p>
        </div>
        <div class="footer">
            <p>&copy; 2024 Your Company. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
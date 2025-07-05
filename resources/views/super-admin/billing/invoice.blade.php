<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $billing->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 40px;
        }
        .company-info h1 {
            color: #667eea;
            margin: 0 0 10px 0;
        }
        .invoice-details {
            text-align: right;
        }
        .invoice-details h2 {
            color: #333;
            margin: 0 0 10px 0;
        }
        .billing-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }
        .billing-to, .billing-from {
            width: 45%;
        }
        .billing-to h3, .billing-from h3 {
            color: #667eea;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 5px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .invoice-table th,
        .invoice-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .invoice-table th {
            background-color: #667eea;
            color: white;
        }
        .invoice-table .text-right {
            text-align: right;
        }
        .invoice-total {
            text-align: right;
            margin-bottom: 40px;
        }
        .invoice-total table {
            margin-left: auto;
            min-width: 300px;
        }
        .invoice-total .total-row {
            background-color: #667eea;
            color: white;
            font-weight: bold;
        }
        .invoice-footer {
            border-top: 2px solid #667eea;
            padding-top: 20px;
            text-align: center;
            color: #666;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-paid { background-color: #28a745; }
        .status-pending { background-color: #ffc107; color: #333; }
        .status-overdue { background-color: #dc3545; }
        .status-cancelled { background-color: #6c757d; }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        @media print {
            body { background: white; }
            .print-button { display: none; }
            .invoice-container { box-shadow: none; }
        }
    </style>
</head>
<body>
    <button class="print-button" onclick="window.print()">Print Invoice</button>
    
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>Super Admin Panel</h1>
                <p>
                    Professional E-commerce Solutions<br>
                    Email: admin@yourdomain.com<br>
                    Phone: (555) 123-4567
                </p>
            </div>
            <div class="invoice-details">
                <h2>INVOICE</h2>
                <p>
                    <strong>Invoice #:</strong> {{ $billing->invoice_number }}<br>
                    <strong>Date:</strong> {{ $billing->billing_date->format('M d, Y') }}<br>
                    <strong>Due Date:</strong> {{ $billing->due_date->format('M d, Y') }}<br>
                    <strong>Status:</strong> 
                    <span class="status-badge status-{{ $billing->status }}">
                        {{ $billing->status_name }}
                    </span>
                </p>
            </div>
        </div>
        
        <!-- Billing Information -->
        <div class="billing-section">
            <div class="billing-from">
                <h3>From</h3>
                <p>
                    <strong>Super Admin Panel</strong><br>
                    Professional E-commerce Solutions<br>
                    123 Business Street<br>
                    City, State 12345<br>
                    admin@yourdomain.com<br>
                    (555) 123-4567
                </p>
            </div>
            <div class="billing-to">
                <h3>Bill To</h3>
                <p>
                    <strong>{{ $billing->company->name }}</strong><br>
                    {{ $billing->company->email }}<br>
                    {{ $billing->company->domain }}<br>
                    @if($billing->company->phone)
                        {{ $billing->company->phone }}<br>
                    @endif
                    @if($billing->company->address)
                        {{ $billing->company->address }}<br>
                    @endif
                    @if($billing->company->city || $billing->company->state)
                        {{ $billing->company->city }}@if($billing->company->city && $billing->company->state), @endif{{ $billing->company->state }}<br>
                    @endif
                    @if($billing->company->country)
                        {{ $billing->company->country }}
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Invoice Items -->
        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="text-right">Billing Period</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>{{ $billing->package->name }}</strong><br>
                        {{ $billing->package->description }}<br>
                        <small>Billing Cycle: {{ ucfirst($billing->billing_cycle) }}</small>
                    </td>
                    <td class="text-right">
                        {{ $billing->billing_date->format('M d, Y') }} - 
                        @if($billing->billing_cycle === 'monthly')
                            {{ $billing->billing_date->addMonth()->subDay()->format('M d, Y') }}
                        @elseif($billing->billing_cycle === 'yearly')
                            {{ $billing->billing_date->addYear()->subDay()->format('M d, Y') }}
                        @else
                            Lifetime
                        @endif
                    </td>
                    <td class="text-right">{{ $billing->formatted_amount }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Invoice Total -->
        <div class="invoice-total">
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td class="text-right">{{ $billing->formatted_amount }}</td>
                </tr>
                <tr>
                    <td><strong>Tax (0%):</strong></td>
                    <td class="text-right">$0.00</td>
                </tr>
                <tr class="total-row">
                    <td><strong>Total Due:</strong></td>
                    <td class="text-right"><strong>{{ $billing->formatted_amount }}</strong></td>
                </tr>
            </table>
        </div>
        
        <!-- Payment Information -->
        @if($billing->payment_method || $billing->transaction_id)
            <div style="margin-bottom: 30px;">
                <h3 style="color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 5px;">Payment Information</h3>
                @if($billing->payment_method)
                    <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $billing->payment_method)) }}</p>
                @endif
                @if($billing->transaction_id)
                    <p><strong>Transaction ID:</strong> {{ $billing->transaction_id }}</p>
                @endif
                @if($billing->paid_at)
                    <p><strong>Payment Date:</strong> {{ $billing->paid_at->format('M d, Y g:i A') }}</p>
                @endif
            </div>
        @endif
        
        <!-- Notes -->
        @if($billing->notes)
            <div style="margin-bottom: 30px;">
                <h3 style="color: #667eea; border-bottom: 2px solid #667eea; padding-bottom: 5px;">Notes</h3>
                <p>{{ $billing->notes }}</p>
            </div>
        @endif
        
        <!-- Footer -->
        <div class="invoice-footer">
            <p>
                Thank you for your business!<br>
                For questions about this invoice, please contact us at admin@yourdomain.com<br>
                <small>This invoice was generated on {{ now()->format('M d, Y g:i A') }}</small>
            </p>
        </div>
    </div>
</body>
</html>

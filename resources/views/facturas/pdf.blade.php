{{-- resources/views/facturas/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>Factura #{{ $proceso->id }}</title>
    <style>
        /* Estilos iguales a los que ya tienes en generateSingleInvoice */
        body { font-family: Arial; font-size: 12px; }
        .invoice-header { display: flex; justify-content: space-between; }
        .company-name { font-size: 24px; font-weight: bold; color: #2C3E50; }
        .detail-table { width: 100%; border-collapse: collapse; }
        .detail-table th, .detail-table td { border: 1px solid #ddd; padding: 8px; }
        .detail-table th { background-color: #2C3E50; color: white; }
    </style>
</head>
<body>
    <div class="invoice-header">
        <div class="company-info">
            <h1 class="company-name">SISTEMA JURÍDICO DE TRÁNSITO</h1>
            <div>Nit: 900.000.000-1</div>
            <div>Fecha: {{ $proceso->created_at->format('d/m/Y') }}</div>
        </div>
        <div class="invoice-info">
            <h2>FACTURA</h2>
            <div>No. {{ str_pad($proceso->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
    
    <!-- El resto del contenido de la factura -->
    <!-- ... -->
</body>
</html>
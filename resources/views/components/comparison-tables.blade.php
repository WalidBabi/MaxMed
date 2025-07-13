@props(['products' => []])

@if(count($products) > 1)
<div class="comparison-table">
    <h3>Product Comparison</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Feature</th>
                @foreach($products as $product)
                    <th>{{ $product->name }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Brand</td>
                @foreach($products as $product)
                    <td>{{ $product->brand ? $product->brand->name : "N/A" }}</td>
                @endforeach
            </tr>
            <tr>
                <td>Category</td>
                @foreach($products as $product)
                    <td>{{ $product->category ? $product->category->name : "N/A" }}</td>
                @endforeach
            </tr>
            <tr>
                <td>Warranty</td>
                @foreach($products as $product)
                    <td>Manufacturer warranty</td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>
@endif
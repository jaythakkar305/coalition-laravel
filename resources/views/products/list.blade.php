<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Product Name</th>
                <th scope="col">Quantity in Stock</th>
                <th scope="col">Price per Item</th>
                <th scope="col">Datetime submitted</th>
                <th scope="col">Total value number</th>
                <th scope="col">Edit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $row['product_name'] }}</td>
                    <td>{{ $row['quantity_in_stock'] }}</td>
                    <td>{{ $row['price_per_item'] }}</td>
                    <td>{{ \Carbon\Carbon::parse($row['created_at'])->format('M d Y h:i a') }}</td>
                    <td>{{ (float) $row['total_value_number'] }}</td>
                    <td><a class="btn btn-primary btn-sm editbutton" data-id="{{ $row['id'] }}" data-product_name="{{ $row['product_name'] }}" data-quantity_in_stock="{{ $row['quantity_in_stock'] }}" data-price_per_item="{{ $row['price_per_item'] }}" > Edit</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center">No data available</td>
                </tr>
            @endforelse
        </tbody>
        @if ($data->isNotEmpty())
         <tfoot>
            <tr>
                <td colspan="4" class="text-center"></td>
              <td><b>Sum</b></td>
              <td><b>{{ $data->sum('total_value_number') }}</b></td>
              <td></td>
            </tr>
          </tfoot>
        @endif        
    </table>

    {{-- Pagination Links --}}
    <div class="d-flex justify-content-center">
        {{ $data->links() }}
    </div>
</div>
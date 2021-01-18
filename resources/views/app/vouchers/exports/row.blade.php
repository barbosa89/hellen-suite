<tr>
    <th scope="row">{{ $description ?? '' }}</th>
    <td>$ {{ isset($price) ? number_format($price, 2, ',', '.') : 0 }}</td>
    <td>{{ $quantity ?? 0 }}</td>
    <td>$ {{ isset($value) ? number_format($value, 2, ',', '.') : 0 }}</td>
</tr>

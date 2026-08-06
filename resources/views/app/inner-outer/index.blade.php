<table>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Jenis</th>
            <th>Nama</th>
            <th>Material</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
        <tr>
            <td>{{ $item->kode }}</td>
            <td>{{ $item->jenis }}</td>
            <td>{{ $item->nama }}</td>
            <td>{{ $item->material }}</td>
            <td>
                <a href="">Edit</a>
                <form action="" method="POST">Hapus</form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
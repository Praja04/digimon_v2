@extends('layouts.component.main')

@section('title','Packaging Sampling')

@section('content')
<div class="page-content">
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Packaging Sampling Online</h4>
            </div>

            <div class="card-body">

                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No SPB</th>
                            <th>Jenis Incoming</th>
                            <th>Jenis Material</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($incomings as $item)

                    <tr>
                                <td>9000{{ $loop->iteration }}</td>
                                <td>{{ $item->kategori }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>
                                                <span class="badge bg-success">
                                                    Sudah Sampling
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">
                                                Tidak ada data
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>

                </table>

            </div>
        </div>

    </div>
</div>
@endsection
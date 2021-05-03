@extends('admin.layouts.index')


@section('content')
<div id="page-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Đơn vị giao hàng
                    <small>Thêm</small>
                </h1>
                <form action="{{ route('ship.add') }}" method="POST" enctype="multipart/form-data">

                    @csrf
                    <div class="form-group">
                        <label for="name">Tên đơn vị:</label>
                        <input type="text" class="form-control" placeholder="Nhập tên đơn vị" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="price">Phí dịch vụ:</label>
                        <input type="number" class="form-control" placeholder="Nhập phí dịch vụ" id="price" name="price" min=1 required>
                    </div>
                    <button type="submit" class="btn btn-primary">Thêm</button>
                  </form>
            </div>
        </div>
    </div>    
</div>
@endsection
@extends('admin.layouts.index')


@section('content')
    <div id="page-wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Đơn hàng
                        <small>Danh sách</small>
                    </h1>
                </div>
                <!-- /.col-lg-12 -->
                <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                    <thead>
                        <tr align="center">
                            <th>Số thứ tự</th>
                            <th>Khách hàng</th>
                            <th>Mã khuyến mãi</th>
                            <th>Thành tiền</th>
                            <th>Trang thái</th>
                            <th>Sản phẩm</th>
                            <th>Mã đơn hàng</th>
                            <th>Chức năng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td>
                                    <a href="{{ route('order.delete',['id' => $order['id']]) }}"><i class="fa fa-times" aria-hidden="true"></i></a>
                                    <a href="{{ route('order.cancle',['id' => $order['id']]) }}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    <a href="{{ route('order.complete',['id' => $order['id']]) }}"><i class="fa fa-money" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
@endsection
@extends('layouts.admin')

@section('title', 'offers')


@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>offers</h1>

            <ol class="breadcrumb">
                <li> <a href="{{url('admins')}}"><i class="fa fa-dashboard"></i>dashboard</a>
                </li>
                <li class="active"><i class="fa fa-users"></i>offers</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">
                    <form action="#" method="get">

                        <div class="row">

                             

                            <div class="col-md-4">
                                 
                                @if (auth('admin')->user()->isAbleTo('create-offers'))
                                    <a href="{{url('admins/offers/create')}}"
                                    class="btn btn-primary"><i class="fa fa-plus"></i>add
                                    </a>
                                @else
                                    <button class="btn btn-primary"disabled><i class="fa fa-plus"></i>Add </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div> {{-- end of box header --}}

                <div class="box-body">

                    <table class="table table-hover" id="myTable">

                        <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>price</th>
                                    <th>discount</th>
                                    <th>classes count</th>
                                    <th>action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($offers as $offer)
                                    <tr>
                                        <td>{{$offer->id}}</td>
                                        <td>{{$offer->price}}</td>
                                        <td>{{$offer->discount}} %</td>
                                        <td>{{$offer->classes_count}}</td>
                                        <td>
                                            {{-- edit --}}
                                            @if (auth('admin')->user()->isAbleTo('update-offers'))
                                                <a href="{{url('admins/offers/edit/' . $offer->id)}}" style="color: #fff;
                                                    background-color: #17a2b8;
                                                    border-color: #17a2b8;" rel="tooltip" title="" class="btn btn-info btn-sm "
                                                        data-original-title="edit">
                                                        <i class="fa fa-edit">edit</i>
                                                </a>
                                            @else
                                                <button class="btn btn-info btn-sm"type="submit" value="" disabled>
                                                    <i class="fa fa-edit">edit</i>
                                                </button>
                                            @endif

                                            {{-- delete --}}
                                            @if (auth('admin')->user()->isAbleTo('delete-offers'))
                                                <a href="{{url('admins/offers/delete/' . $offer->id)}}" tyle="color:#fff!important;" rel="tooltip" title="" class="btn btn-danger  btn-sm">
                                                    <i class="fa fa-1x fa-trash">delete</i>
                                                </a> 
                                            @else
                                                <button class="btn btn-danger btn-sm"type="submit" value="" disabled>
                                                    <i class="fa fa-trash">delete</i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                
                            </tbody>

                        </table> {{-- end of table --}}

                </div> {{-- end of box body --}}

            </div> {{-- end of box --}}

        </section><!-- end of content -->

    </div><!-- end of content wrapper -->

@endsection

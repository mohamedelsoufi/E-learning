@extends('layouts.admin')

@section('title', 'years')


@section('content')

    <div class="content-wrapper">

        <section class="content-header">

            <h1>years</h1>

            <ol class="breadcrumb">
                <li> <a href="{{url('admins')}}"><i class="fa fa-dashboard"></i>dashboard</a>
                </li>
                <li> <a href="{{url('admins/curriculums')}}"><i class="fa fa-dashboard"></i>curriculum</a>
                </li>
                <li> <a href="{{url('admins/levels?curriculum='.$curriculum_id)}}"><i class="fa fa-dashboard"></i>levels</a>
                </li>
                <li class="active"><i class="fa fa-users"></i>years</li>
            </ol>
        </section>

        <section class="content">

            <div class="box box-primary">

                <div class="box-header with-border">
                    <form action="#" method="get">

                        <div class="row">

                             

                            <div class="col-md-4">
                                 
                                @if (auth('admin')->user()->isAbleTo('create-curriculums'))
                                    <a href="{{url('admins/years/create?' . $parms)}}"
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

                    <table class="table table-hover">

                        <thead class="thead-dark">
                                <tr>
                                    <th>#</th>
                                    <th>name</th>
                                    <th>description</th>
                                    <th>status</th>
                                    <th>terms count</th>
                                    <th>action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($years as $year)
                                    <tr>
                                        <td>{{$year->id}}</td>
                                        <td>{{$year->translate('en')->name}}</td>
                                        <td>
                                            {{$year->Level->Curriculum->Country->translate('en')->name}} ->
                                            {{$year->Level->Curriculum->translate('en')->name}} ->
                                            {{$year->Level->translate('en')->name}}
                                        </td>
                                        <td>{{$year->getStatus()}}</td>
                                        <td><a href="terms?curriculum={{$curriculum_id}}&&level={{$level_id}}&&year={{$year->id}}">{{count($year->Terms->where('status', '!=', -1))}}</a></td>
                                        <td>
                                            {{-- edit --}}
                                            @if (auth('admin')->user()->isAbleTo('update-curriculums'))
                                                <a href="{{url('admins/years/edit/' . $year->id . '?' . $parms)}}" style="color: #fff;
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
                                            @if (auth('admin')->user()->isAbleTo('delete-curriculums'))
                                                <a href="{{url('admins/years/delete/' . $year->id . '?' . $parms)}}" tyle="color:#fff!important;" rel="tooltip" title="" class="btn btn-danger  btn-sm">
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

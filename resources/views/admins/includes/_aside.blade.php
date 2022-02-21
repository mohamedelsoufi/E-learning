<aside class="main-sidebar">

    <section class="sidebar">

        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{auth('admin')->user()->getImage()}}" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
                <p>ahmed maher</p>
                <a href="#"><i class="fa fa-circle text-success"></i>statue</a>
            </div>
        </div>

        <ul class="sidebar-menu" data-widget="tree">
            <li class="{{request()->is('admins')? 'active':''}}">
                <a href="{{url('admins')}}"><i class="fa fa-users"></i><span>dashboard</span></a>
            </li>

            @if (auth('admin')->user()->isAbleTo('read-admins'))
                <li class="{{(request()->is('admins/admins') || request()->is('admins/admins/*'))? 'active':''}}">
                    <a href="{{url('admins/admins')}}"><i class="fa fa-users"></i><span>admins</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-roles'))
                <li class="{{(request()->is('admins/roles') || request()->is('admins/roles/*'))? 'active':''}}">
                    <a href="{{url('admins/roles')}}"><i class="fa fa-users"></i><span>roles</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-countries'))
                <li class="{{(request()->is('admins/countries') || request()->is('admins/countries/*'))? 'active':''}}">
                    <a href="{{url('admins/countries')}}"><i class="fa fa-users"></i><span>countries</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-curriculums'))
                <li class="{{(request()->is('admins/curriculums') || request()->is('admins/curriculums/*'))? 'active':''}}">
                    <a href="{{url('admins/curriculums')}}"><i class="fa fa-users"></i><span>curriculums</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-promo_codes'))
                <li class="{{(request()->is('admins/promo_codes')|| request()->is('admins/promo_codes/*'))? 'active':''}}">
                    <a href="{{url('admins/promo_codes')}}"><i class="fa fa-users"></i><span>promo_codes</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-questions'))
                <li class="{{(request()->is('admins/questions') || request()->is('admins/questions/*'))? 'active':''}}">
                    <a href="{{url('admins/questions')}}"><i class="fa fa-users"></i><span>questions</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-students'))
                <li class="{{(request()->is('admins/students') || request()->is('admins/students/*'))? 'active':''}}">
                    <a href="{{url('admins/students')}}"><i class="fa fa-users"></i><span>students</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-teachers'))
                <li class="{{(request()->is('admins/teachers') || request()->is('admins/teachers/*'))? 'active':''}}">
                    <a href="{{url('admins/teachers')}}"><i class="fa fa-users"></i><span>teachers</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-class_types'))
                <li class="{{(request()->is('admins/levels_cost')|| request()->is('admins/levels_cost/*'))? 'active':''}}">
                    <a href="{{url('admins/levels_cost')}}"><i class="fa fa-users"></i><span>levels cost</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-class_types'))
                <li class="{{(request()->is('admins/countries_cost') || request()->is('admins/countries_cost/*'))? 'active':''}}">
                    <a href="{{url('admins/countries_cost')}}"><i class="fa fa-users"></i><span>countries cost</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-class_types'))
                <li class="{{(request()->is('admins/company_percentages') || request()->is('admins/company_percentages/*'))? 'active':''}}">
                    <a href="{{url('admins/company_percentages')}}"><i class="fa fa-users"></i><span>companies percentage</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-settings'))
                <li class="{{(request()->is('admins/settings/edit') || request()->is('admins/settings/edit/*'))? 'active':''}}">
                    <a href="{{url('admins/settings/edit')}}"><i class="fa fa-users"></i><span>settings</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-class_types'))
                <li class="{{(request()->is('admins/class_types') || request()->is('admins/class_types/*'))? 'active':''}}">
                    <a href="{{url('admins/class_types')}}"><i class="fa fa-users"></i><span>class types</span></a>
                </li>
            @endif
        </ul>
    </section>
</aside>
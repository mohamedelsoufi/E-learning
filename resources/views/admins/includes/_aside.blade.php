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
                <li class="{{request()->is('admins/admins')? 'active':''}}">
                    <a href="{{url('admins/admins')}}"><i class="fa fa-users"></i><span>admins</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-roles'))
                <li class="{{request()->is('admins/roles')? 'active':''}}">
                    <a href="{{url('admins/roles')}}"><i class="fa fa-users"></i><span>roles</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-countries'))
                <li class="{{request()->is('admins/countries')? 'active':''}}">
                    <a href="{{url('admins/countries')}}"><i class="fa fa-users"></i><span>countries</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-curriculums'))
                <li class="{{request()->is('admins/curriculums')? 'active':''}}">
                    <a href="{{url('admins/curriculums')}}"><i class="fa fa-users"></i><span>curriculums</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-curriculums'))
                <li class="{{request()->is('admins/levels')? 'active':''}}">
                    <a href="{{url('admins/levels')}}"><i class="fa fa-users"></i><span>levels</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-curriculums'))
                <li class="{{request()->is('admins/years')? 'active':''}}">
                    <a href="{{url('admins/years')}}"><i class="fa fa-users"></i><span>years</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-curriculums'))
                <li class="{{request()->is('admins/terms')? 'active':''}}">
                    <a href="{{url('admins/terms')}}"><i class="fa fa-users"></i><span>terms</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-curriculums'))
                <li class="{{request()->is('admins/subjects')? 'active':''}}">
                    <a href="{{url('admins/subjects')}}"><i class="fa fa-users"></i><span>subjects</span></a>
                </li>
            @endif

            @if (auth('admin')->user()->isAbleTo('read-promo_codes'))
                <li class="{{request()->is('admins/promo_codes')? 'active':''}}">
                    <a href="{{url('admins/promo_codes')}}"><i class="fa fa-users"></i><span>promo_codes</span></a>
                </li>
            @endif

        </ul>
    </section>
</aside>
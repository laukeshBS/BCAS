 <!-- sidebar menu area start -->
 @php
     $usr = Auth::guard('admin')->user();
 @endphp

 <div id="mySidebar" class="sidebar">
 <button class="openbtn float-right" onclick="toggleNav()" style="margin-top: 23px;">X</button>

    <div class="sidebar-header">
        <div class="logo">
            <a href="{{ route('admin.dashboard') }}">
                <h2 class="text-white">Admin</h2> 
            </a> 
            
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">

                    @if ($usr->can('dashboard.view'))
                    <li class="active">
                        <a href="javascript:void(0)" aria-expanded="true"><i class="ti-dashboard"></i><span>dashboard</span></a>
                        <ul class="collapse">
                            <li class="{{ Route::is('admin.dashboard') ? 'active' : '' }}"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        </ul>
                    </li>
                    @endif

                    @if ($usr->can('role.create') || $usr->can('role.view') ||  $usr->can('role.edit') ||  $usr->can('role.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-tasks"></i><span>
                            Roles & Permissions
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.roles.create') || Route::is('admin.roles.index') || Route::is('admin.roles.edit') || Route::is('admin.roles.show') ? 'in' : '' }}">
                            @if ($usr->can('role.view'))
                                <li class="{{ Route::is('admin.roles.index')  || Route::is('admin.roles.edit') ? 'active' : '' }}"><a href="{{ route('admin.roles.index') }}">All Roles</a></li>
                            @endif
                            @if ($usr->can('role.create'))
                                <li class="{{ Route::is('admin.roles.create')  ? 'active' : '' }}"><a href="{{ route('admin.roles.create') }}">Create Role</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    
                    @if ($usr->can('admin.create') || $usr->can('admin.view') ||  $usr->can('admin.edit') ||  $usr->can('admin.delete'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                            Admins
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.admins.create') || Route::is('admin.admins.index') || Route::is('admin.admins.edit') || Route::is('admin.admins.show') ? 'in' : '' }}">
                            
                            @if ($usr->can('admin.view'))
                                <li class="{{ Route::is('admin.admins.index')  || Route::is('admin.admins.edit') ? 'active' : '' }}"><a href="{{ route('admin.admins.index') }}">All Admins</a></li>
                            @endif

                            @if ($usr->can('admin.create'))
                                <li class="{{ Route::is('admin.admins.create')  ? 'active' : '' }}"><a href="{{ route('admin.admins.create') }}">Create Admin</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if ($usr->can('menu.create') || $usr->can('menu.view') ||  $usr->can('menu.edit') ||  $usr->can('menu.delete')||  $usr->can('menu.approve'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-user"></i><span>
                           Menus
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.menus.create') || Route::is('admin.menus.index') || Route::is('admin.menus.edit') || Route::is('admin.menus.show') ? 'in' : '' }}">
                            
                            @if ($usr->can('menu.view'))
                                <li class="{{ Route::is('admin.menus.index')  || Route::is('admin.menus.edit') ? 'active' : '' }}"><a href="{{ route('admin.menus.index') }}">All Menus</a></li>
                            @endif

                            @if ($usr->can('menu.create'))
                                <li class="{{ Route::is('admin.menus.create')  ? 'active' : '' }}"><a href="{{ route('admin.menus.create') }}">Create Menu</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif
                    @if ($usr->can('course.create') || $usr->can('course.view') ||  $usr->can('course.edit') ||  $usr->can('course.delete')||  $usr->can('course.approve'))
                    <li>
                        <a href="javascript:void(0)" aria-expanded="true"><i class="fa fa-building-o"></i><span>
                          Divisions
                        </span></a>
                        <ul class="collapse {{ Route::is('admin.division.training.course.create') || Route::is('admin.division.training.course.index') || Route::is('admin.division.training.course.edit') || Route::is('admin.division.training.course.show') ? 'in' : '' }}">
                            
                            @if ($usr->can('course.view'))
                                <li class="{{ Route::is('admin.division.training.course.index')  || Route::is('admin.division.training.course.edit') ? 'active' : '' }}"><a href="{{ route('admin.division.training.course.index') }}">All Courses</a></li>
                            @endif

                            @if ($usr->can('course.create'))
                                <li class="{{ Route::is('admin.division.training.course.create')  ? 'active' : '' }}"><a href="{{ route('admin.division.training.course.create') }}">Create course</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- sidebar menu area end -->
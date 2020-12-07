<div class="left main-sidebar">

    <div class="sidebar-inner leftscroll">

        <div id="sidebar-menu">

            <ul>

                <li class="submenu">
                    <a @if(Request::route()->getName() == 'admin-dashboard') class="active"
                       @else class="" @endif href="{{route('admin-dashboard')}}">
                    <i class="fas fa-tachometer-alt"></i><span> Dashboard </span>
                    </a>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fas fa-users"></i> <span> Tab Listing </span> <span
                            class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="admin-listing.html">Admin</a></li>
                        <li><a href="consultant.html">Consultans</a></li>
                        <li><a href="editor-listing.html">Editor</a></li>
                        <li><a href="internee-listing.html">Internee</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="far fa-building"></i><span> Companies </span> <span
                            class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="company-listing.html">Listing</a></li>
                        <li><a href="hotel-brand-listing.html">Hotel Listing</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="fas fa-cubes"></i> <span> Packages </span> <span
                            class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="forms-general.html">Services</a></li>
                        <li><a href="forms-select2.html">Packages</a></li>
                    </ul>
                </li>

                <li class="submenu">
                    <a href="#"><i class="far fa-calendar-alt"></i><span> Scheduling </span> <span
                            class="menu-arrow"></span></a>
                    <ul class="list-unstyled">
                        <li><a href="calendar.html">Calendar</a></li>
                        <li><a href="pro-users.html">Listing</a></li>
                        <li><a href="completed-visits.html">Completed Visits Listing</a></li>
                    </ul>
                </li>
                <li class="submenu">
                    <a href="#"><i class="fab fa-readme"></i> <span> Reports </span> <span
                            class="menu-arrow"></span></a>
                    <ul class="list-unstyled">

                        <li><a href="completed-visits.html">Listing</a></li>
                    </ul>
                </li>
                <li>
                    <a href="{{route('feedbacks')}}"><i class="fab fa-readme"></i> <span> Feedbacks </span> <span
                            class="menu-arrow"></span></a>
                </li>
            </ul>

            <div class="clearfix"></div>

        </div>

        <div class="clearfix"></div>

    </div>

</div>

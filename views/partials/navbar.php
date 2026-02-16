<nav class="header-navbar navbar navbar-with-menu navbar-fixed-top navbar-semi-dark navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-header">
            <ul class="nav navbar-nav">
                <li class="nav-item mobile-menu hidden-md-up float-xs-left">
                    <a class="nav-link nav-menu-main menu-toggle hidden-xs">
                        <i class="icon-menu5 font-large-1"> </i>
                    </a>
                </li>
                <li class="nav-item"><a href="/AIS/dashboard" class="navbar-brand nav-link">
                    <!-- <img loading="lazy"
                                alt="branding logo" src="Public/assets/img/logo/logo-header.png"
                                data-expand="Public/assets/img/logo/logo-header.png"
                                data-collapse="Public/assets/img/logo/logo/logo-80x80.png"
                                class="brand-logo height-50"> -->
                                <h1  id="logoText" class="brand-logo height-50"><span style="color:yellow;font-weight: 900;font-family: system-ui;">DTT </span><span style="color:#3BAFDA;">EDGE</span></h1>
                            </a>
                            </li>

                <li class="nav-item hidden-md-up float-xs-right">
                    <a  data-toggle="collapse" data-target="#navbar-mobile" class="nav-link open-navbar-container"><i
                                class="icon-ellipsis pe-2x icon-icon-rotate-right-right"></i></a></li>
            </ul>
        </div>
        <div class="navbar-container content container-fluid">
            <div id="navbar-mobile" class="collapse navbar-toggleable-sm">
                <ul class="nav navbar-nav">
                    <li class="nav-item hidden-sm-down">
                        <a id="sidebarToggle" class="nav-link nav-menu-main menu-toggle hidden-xs">
                            <i class="icon-menu5"></i>
                        </a>
                                </li>
                    <li class="nav-item hidden-sm-down"><a href="#" class="nav-link nav-link-expand"><i
                                    class="icon icon-expand2"></i></a></li>
                    <!-- <li class="nav-item hidden-sm-down">
                        <input type="text"
                                                               placeholder="Search Customer... "
                                                               id="head-customerbox"
                                                               class="nav-link menu-search form-control round"/></li> -->
                </ul>
                <div id="head-customerbox-result" class="dropdown dropdown-notification"></div>
                <ul class="nav navbar-nav float-xs-right">

                    <li class="dropdown dropdown-notification nav-item"><a href="#" data-toggle="dropdown"
                                                                           class="nav-link nav-link-label"><i
                                    class="ficon icon-bell4"></i><span
                                    class="tag tag-pill tag-default tag-danger tag-default tag-up"
                                    id="taskcount">0</span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <h6 class="dropdown-header m-0"><span
                                            class="grey darken-2">Pending Tasks</span>
                                </h6>
                            </li>
                            <li class="list-group scrollable-container" id="tasklist"></li>
                            <li class="dropdown-menu-footer"><a href=""
                                                                class="dropdown-item text-muted text-xs-center">Manage tasks</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown dropdown-notification nav-item"><a href="#" data-toggle="dropdown"
                                                                           class="nav-link nav-link-label"><i
                                    class="ficon icon-mail6"></i><span
                                    class="tag tag-pill tag-default tag-info tag-default tag-up">0</span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">

                            <li class="dropdown-menu-header">
                                <h6 class="dropdown-header m-0"><span
                                            class="grey darken-2">Messages</span>
                                </h6>
                            </li>
                            <li class="list-group scrollable-container">


                                
                            </li>
                            <li class="dropdown-menu-footer"><a href="#"
                                                                class="dropdown-item text-muted text-xs-center">Read all messages</a>
                            </li>
                        </ul>
                    </li>
                    <li class="dropdown dropdown-user nav-item">
                        <a href="#" data-toggle="dropdown" class="dropdown-toggle nav-link dropdown-user-link"><span class="avatar avatar-online"><img loading="lazy"
                                        src="Public/assets/img/logo/example.png"
                                        alt="avatar"><i></i></span></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="/AIS/Profile" class="dropdown-item">
                                <i class="icon-head"></i>Profile</a>
                            <div class="dropdown-divider"></div>
                                        <form action="/AIS/logout" method="POST" style="margin: 0;">
      <input type="hidden" name="_method" value="DELETE">
        <a href="" class="dropdown-item"> <button type="submit" style="background: none; border: none; display: flex; align-items: center; gap: 10px; cursor: pointer;">
    <i class="icon-power3"></i>Logout
</button></a>
        </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
<script>
  // Wait until page is fully loaded
  window.onload = function () {
    var toggleBtn = document.getElementById("sidebarToggle");
    var logo = document.getElementById("logoText");

    var isCollapsed = false;

    toggleBtn.onclick = function (e) {
      e.preventDefault(); // stop page reload
      console.log("clicked");
      isCollapsed = !isCollapsed;

      if (isCollapsed) {
         logo.innerHTML = `<span style="color:yellow;font-weight:900;font-family:system-ui;">R</span><span style="color:#3BAFDA;">E</span>`;
      } else {
        logo.innerHTML = `<span style="color:yellow;font-weight:900;font-family:system-ui;">DTT </span><span style="color:#3BAFDA;">EDGE</span>`;
      }
    };
  };
</script>




<?php 
 $logged_user_firstname = $_SESSION['user']['firstname'];
 $logged_user_lastname = $_SESSION['user']['lastname'];
 $logged_user_type = $_SESSION['user']['UserType'] ?? 0;
   
 ?>
<div data-scroll-to-active="true" class="main-menu menu-static menu-dark menu-accordion menu-shadow" id="side">  
    <!-- main menu header-->
    <div class="main-menu-header">
        <div>
            <div class="dropdown profile-element"> <span>
                            <img loading="lazy" alt="image" class="img-circle "
                                 src="Public/assets/img/logo/example2.png">
                                 
                             </span>
                <a data-toggle="dropdown" class="dropdown-toggle block" href="#" aria-expanded="false">
                    <span class="clear white">  <span
                                class="text-xs"><?= htmlspecialchars($logged_user_firstname) ?> <?= htmlspecialchars($logged_user_lastname ) ?><b
                                    class="caret"></b></span> </span> </a>
                <ul class="dropdown-menu animated m-t-xs">
                    <li>
                        <a href="/AIS/Profile">&nbsp;(admin)</a></li>

                    </li>
                    <li>
                         <a href="">
                                  <form action="/AIS/logout" method="POST" style="margin: 0;">
      <input type="hidden" name="_method" value="DELETE">
        <a href="" class="dropdown-item"> <button type="submit" style="background: none; border: none; display: flex; align-items: center; gap: 10px; cursor: pointer;">
    &nbsp;Logout
</button></a>
        </form>
                       </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
    <!-- / main menu header-->
    <!-- main menu content-->
    <div class="main-menu-content">
        <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
              <?php if (in_array($logged_user_type, [1, 5])): ?>
            <li  class="nav-item <?= urlIs('/AIS/dashboard')  ? 'active current-page' : '' ?>">
                <a href="/AIS/dashboard"> <i class="icon-dashboard"></i><span
                            class="menu-title"> Dashboard</span></a>
            </li>
              <?php endif; ?>
            <?php if (in_array($logged_user_type, [1, 2, 3, 5])): ?>
                            <li class="navigation-header"><span
                            data-i18n="nav.category.support"> Sales</span><i
                            data-toggle="tooltip"
                            data-placement="right"
                            data-original-title="Sales"
                            class="icon-ellipsis icon-ellipsis"></i>
                </li>
                <li class="nav-item has-sub <?= urlIs('/AIS/create') || urlIs('/AIS/manage') || urlIs('/AIS/quote') || urlIs('/AIS/quote-manage') ? 'active current-page open' : '' ?>">
                    <a href=""> <i class="icon-plus"></i> <span
                                class="menu-title">Sales  <i class="icon-arrow"></i></span></a>
                    <ul class="menu-content">
                        <li>
                            <a href="/AIS/create">New Invoice</a>
                        </li>
                        <li>
                            <a href="/AIS/manage">Manage Invoices</a>
                        </li>
                        <li>
                            <a href="/AIS/quote">New Quote</a>
                        </li>
                        <li>
                            <a href="/AIS/quote-manage">Manage Quotes</a>
                        </li>
                    </ul>
                </li>
               
                <li class="nav-item has-sub <?= urlIs('/AIS/recur-dashboard') || urlIs('/AIS/recur-create') || urlIs('/AIS/rec-manage') ? 'active current-page open' : '' ?>">
                    <a href=""> <i class="icon-android-calendar"></i> <span
                                class="menu-title">Recurring Sales<i
                                    class="icon-arrow"></i></span></a>
                    <ul class="menu-content">
                        <li>
                            <a href="/AIS/recur-dashboard">Dashboard</a>
                        </li>
                        <li>
                            <a href="/AIS/recur-create">New Invoice</a>
                        </li>
                        <li>
                            <a href="/AIS/rec-manage">Manage Invoices</a>
                        </li>

          
                    </ul>
                </li>


                <li class="nav-item has-sub <?= urlIs('/AIS/purchase') || urlIs('/AIS/purchase-manage') ? 'active current-page open' : '' ?>">
                    <a href=""> <i class="icon-file"></i><span
                                class="menu-title"> Purchase Order </span><i
                                class="fa arrow"></i> </a>
                    <ul class="menu-content">
                        <li>
                            <a href="/AIS/purchase">New Order</a>
                        </li>
                        <li>
                            <a href="/AIS/purchase-manage">Manage Orders</a>
                        </li>
                    </ul>
                </li>

                 <?php endif; ?>
                <!---------------- end project ----------------->

                 <?php if (in_array($logged_user_type, [1, 4, 5])): ?>
                <li class="navigation-header"><span>Balance</span><i
                            data-toggle="tooltip" data-placement="right"
                            data-original-title="Balance"
                            class="icon-ellipsis icon-ellipsis"></i>
                </li>
                <li class="nav-item has-sub <?= urlIs('/AIS/account-manage') || urlIs('/AIS/account') || urlIs('/AIS/BalanceSheet') || urlIs('/AIS/statement') ? 'active current-page open' : '' ?>">
                    <a href=""> <i class="icon-bank"></i><span
                                class="menu-title"> Accounts</span><i
                                class="fa arrow"></i> </a>
                    <ul class="menu-content">
                        <li>
                            <a href="/AIS/account-manage">Manage Accounts</a>
                        </li>
                        <li>
                            <a href="/AIS/account">New Account</a>
                        </li>
                        <li>
                            <a href="/AIS/BalanceSheet">BalanceSheet</a>
                        </li>
                        <li>
                            <a href="/AIS/statement">Account Statements</a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item has-sub <?= urlIs('/AIS/transactions') || urlIs('/AIS/addTrans') || urlIs('/AIS/Transfer') || urlIs('/AIS/income')  || urlIs('/AIS/expense') ? 'active current-page open' : '' ?>">
                    <a href=""> <i class="icon-exchange"></i><span
                                class="menu-title"> Transactions </span><i
                                class="fa arrow"></i> </a>
                    <ul class="menu-content">
                        <li>
                            <a href="/AIS/transactions">View Transactions</a>
                        </li>
                        <li>
                            <a href="/AIS/addTrans">New Transaction</a>
                        </li>
                        <li>
                            <a href="/AIS/Transfer">New Transfer</a>
                        </li>
                        <li>
                            <a href="/AIS/income">Income</a>
                        </li>
                        <li>
                            <a href="/AIS/expense">Expense</a>
                        </li>

                    </ul>
                </li>


                <li class="navigation-header"><span>Data & Reports</span><i
                            data-toggle="tooltip" data-placement="right"
                            data-original-title="Miscellaneous"
                            class="icon-ellipsis icon-ellipsis"></i>
                </li>


      

                <li class="nav-item <?= urlIs('/AIS/statistics')  ? 'active current-page' : '' ?>"><a href="/AIS/statistics"><i class="icon-android-clipboard"></i><span
                                class="menu-title"> Statistics</span></a></li>


                <li class="nav-item <?= urlIs('/AIS/statement')  ? 'active current-page' : '' ?>"><a href="/AIS/statement "><i class="icon-bank"></i><span
                                class="menu-title"> Account Statements</span></a></li>
                <li class="nav-item <?= urlIs('/AIS/customerstatement')  ? 'active current-page' : '' ?>"><a href="/AIS/customerstatement">
                   <i class="icon-bank"></i>
                    <span class="menu-title">Client Account Statement</span>
                  </a>
                </li>
                <li class="nav-item <?= urlIs('/AIS/SupplierStatement')  ? 'active current-page' : '' ?>"><a href="/AIS/SupplierStatement">
                   <i class="icon-bank"></i>
                    <span class="menu-title">Supplier Account Statement</span>
                  </a>
                </li>

                  <li class="nav-item <?= urlIs('/AIS/incomeStatement')  ? 'active current-page' : '' ?>"><a href="/AIS/incomeStatement">
                   <i class="icon-bank"></i>
                    <span class="menu-title">Calculate Income</span>
                  </a>
                </li>

                   <li class="nav-item <?= urlIs('/AIS/expenseStatement')  ? 'active current-page' : '' ?>" ><a href="/AIS/expenseStatement">
                   <i class="icon-bank"></i>
                    <span class="menu-title">Calculate Expenses</span>
                  </a>
                </li>
                    
                <li class="nav-item <?= urlIs('/AIS/tax')  ? 'active current-page' : '' ?>" ><a href="/AIS/tax">
                   <i class="icon-bank"></i>
                    <span class="menu-title">TAX Statements</span>
                  </a>
                </li>

             <?php endif; ?>
              <?php if (in_array($logged_user_type, [1, 4, 5])): ?>

                    <li class="navigation-header"><span>Configure</span>
                    <i data-toggle="tooltip" data-placement="right" data-original-title="Configure" class="icon-ellipsis icon-ellipsis"></i>
                    </li>


                <li class="nav-item has-sub <?= urlIs('/AIS/company') || urlIs('/AIS/dtformat') || urlIs('/AIS/setgoals') || urlIs('/AIS/email')  || urlIs('/AIS/recaptcha') ? 'active current-page open' : '' ?>">
                    <a href=""> <i class="icon-cog"></i><span
                                class="menu-title"> Settings</span><i
                                class="fa arrow"></i> </a>
                    <ul class="menu-content">
                        <li>
                            <a href="/AIS/company">Company</a>
                        </li>
                        <li>
                            <a href="/AIS/dtformat">Date & Time Format</a>
                        </li>
                 
                        <li>
                            <a href="/AIS/setgoals">Set Goals</a>
                        </li>
                   
                        <li>
                            <a href="/AIS/email">Email Config</a>
                        </li>
                        <!-- <li>
                            <a href="https://billing.ultimatekode.com/neo/settings/billing_terms">Billing Terms</a>
                        </li> -->
                    
                        <li>
                            <a href="/AIS/recaptcha">Security</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item <?= urlIs('/AIS/employees')  ? 'active current-page' : '' ?>"><a href="/AIS/employees"><i class="icon-users"></i><span
                                class="menu-title"> Employees</span></a>
                </li>
        
                 <?php endif; ?>

                <?php if (in_array($logged_user_type, [1, 2, 3, 5])): ?>
                     <li class="nav-item <?= urlIs('/AIS/stock')  ? 'active current-page' : '' ?>">
                            <a href="/AIS/stock"> <i class="icon-plus"></i> <span
                                class="menu-title">Add New Product<i class="icon-arrow"></i></span></a>
                        </li>
                           <li class="nav-item <?= urlIs('/AIS/stock-warehouses-add')  ? 'active current-page' : '' ?>">
                            <a href="/AIS/stock-warehouses-add"> <i class="icon-plus"></i> <span
                                class="menu-title">Add New Warehouse<i class="icon-arrow"></i></span></a>
                            
                        </li>
                             <li class="nav-item <?= urlIs('/AIS/category-add')  ? 'active current-page' : '' ?>">
                                <a href="/AIS/category-add"> <i class="icon-plus"></i> <span
                                class="menu-title">Add New Categories<i class="icon-arrow"></i></span></a>
                        </li>

                   <?php endif; ?>
 
       
       
            </ul>
    </div>
    <!-- /main menu content-->
     
    <!-- main menu footer-->
    <!-- include includes/menu-footer-->
    <!-- main menu footer-->
    <div id="rough"></div>
</div>
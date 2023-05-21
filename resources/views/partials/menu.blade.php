<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">JJLudo</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route("admin.home") }}" class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
                        <p>
                            <i class="fas fa-tachometer-alt">

                            </i>
                            <span>{{ trans('global.dashboard') }}</span>
                        </p>
                    </a>
                </li>
                @can('user_management_access')
                <li class="nav-item has-treeview {{ request()->is('admin/permissions*') ? 'menu-open' : '' }} {{ request()->is('admin/roles*') ? 'menu-open' : '' }} {{ request()->is('admin/users*') ? 'menu-open' : '' }} {{ request()->is('admin/kyc_uploads*') ? 'menu-open' : '' }}">
                    <a class="nav-link nav-dropdown-toggle">
                        <i class="fas fa-users">

                        </i>
                        <p>
                        &nbsp;<span>{{ trans('global.userManagement.title') }}</span>
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('user_access')
                        <li class="nav-item">
                            <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">

                                <p>
                                    <span>{{ trans('global.user.title') }}</span>
                                </p>
                            </a>
                        </li>
                        @endcan
                        <li class="nav-item">
                            <a href="{{ route("admin.kyc_uploads.index") }}" class="nav-link {{ request()->is('admin/kyc_uploads') || request()->is('admin/kyc_uploads/*') ? 'active' : '' }}">

                                <p>
                                    <span>{{ trans('global.kyc.title') }}</span>
                                </p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endcan
                <!-- @can('product_access')
                <li class="nav-item">
                    <a href="{{ route("admin.products.index") }}" class="nav-link {{ request()->is('admin/products') || request()->is('admin/products/*') ? 'active' : '' }}">
                        <i class="fas fa-cogs">

                        </i>
                        <p>
                            <span>{{ trans('global.product.title') }}</span>
                        </p>
                    </a>
                </li>
                @endcan -->


                @can('user_management_access')
                <li class="nav-item has-treeview {{ request()->is('admin/game_listings*') ? 'menu-open' : '' }} {{ request()->is('admin/rooms*') ? 'menu-open' : '' }}">
                    <a class="nav-link nav-dropdown-toggle">
                    <i class="fa fa-gamepad" aria-hidden="true"></i>
                        <p>
                        &nbsp;<span>Game Management</span>
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('user_access')
                        <li class="nav-item">
                            <a href="{{ route("admin.game_listings.index") }}" class="nav-link {{ request()->is('admin/game_listings') || request()->is('admin/game_listings/*') ? 'active' : '' }}">

                                <p>
                                    <span>Games</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.rooms.index") }}" class="nav-link {{ request()->is('admin/rooms') || request()->is('admin/rooms/*') ? 'active' : '' }}">

                                <p>
                                    <span>Rooms</span>
                                </p>
                            </a>
                        </li>

                        <!-- <li class="nav-item">
                            <a href="{{ route("admin.refer_commissions.index") }}" class="nav-link {{ request()->is('admin/refer_commissions') || request()->is('admin/refer_commissions/*') ? 'active' : '' }}">
                                <i class="fas fa-user">

                                </i>
                                <p>
                                    <span>Refer-Commission</span>
                                </p>
                            </a>
                        </li> -->
                        @endcan

                    </ul>
                </li>
                @endcan

                @can('user_management_access')
                <li class="nav-item has-treeview {{ request()->is('admin/transactions*') ? 'menu-open' : '' }} {{ request()->is('admin/game_history*') ? 'menu-open' : '' }} {{ request()->is('admin/battle_transactions*') ? 'menu-open' : '' }} {{ request()->is('admin/wallet_transactions*') ? 'menu-open' : '' }} {{ request()->is('admin/referral_transactions*') ? 'menu-open' : '' }} {{ request()->is('admin/admin_commission_histories*') ? 'menu-open' : '' }} {{ request()->is('admin/penalty_histories*') ? 'menu-open' : '' }}">
                    <a class="nav-link nav-dropdown-toggle">
                    <i class="fa fa-history" aria-hidden="true"></i>
                        <p>
                        &nbsp;<span>History</span>
                            <i class="right fa fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        @can('user_access')
                        <li class="nav-item">
                            <a href="{{ route("admin.game_history.index") }}" class="nav-link {{ request()->is('admin/game_history') || request()->is('admin/game_history/*') ? 'active' : '' }}">

                                <p>
                                    <span>Game History</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.transactions.index") }}" class="nav-link {{ request()->is('admin/transactions') || request()->is('admin/transactions/*') ? 'active' : '' }}">

                                <p>
                                    <span>All Transactions History</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.transactions.battle_transactions") }}" class="nav-link {{ request()->is('admin/battle_transactions') || request()->is('admin/battle_transactions/*') ? 'active' : '' }}">

                                <p>
                                    <span>Battle Transactions History</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.transactions.wallet_transactions") }}" class="nav-link {{ request()->is('admin/wallet_transactions') || request()->is('admin/wallet_transactions/*') ? 'active' : '' }}">

                                <p>
                                    <span>Wallet Transactions History</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.transactions.referral_transactions") }}" class="nav-link {{ request()->is('admin/referral_transactions') || request()->is('admin/referral_transactions/*') ? 'active' : '' }}">

                                <p>
                                    <span>Referral Transactions History</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.admin_commission_histories.index") }}" class="nav-link {{ request()->is('admin/admin_commission_histories') || request()->is('admin/admin_commission_histories/*') ? 'active' : '' }}">

                                <p>
                                    <span>Admin Commission Transactions</span>
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route("admin.transactions.penalty_histories") }}" class="nav-link {{ request()->is('admin/penalty_histories') || request()->is('admin/penalty_histories/*') ? 'active' : '' }}">

                                <p>
                                    <span>Penalty History Transactions</span>
                                </p>
                            </a>
                        </li>
                        @endcan

                        {{-- <li class="nav-item">
                            <a href="{{ route("admin.room_historys.index") }}" class="nav-link {{ request()->is('admin/room_historys') || request()->is('admin/room_historys/*') ? 'active' : '' }}">
                        <i class="fas fa-user">

                        </i>
                        <p>
                            <span>Room-History</span>
                        </p>
                        </a>
                </li> --}}


            </ul>
            </li>
            @endcan

            @can('user_management_access')
            <li class="nav-item has-treeview {{ request()->is('admin/commission_limit_managements*') ? 'menu-open' : '' }} {{ request()->is('admin/admin_commissions*') ? 'menu-open' : '' }} {{ request()->is('admin/battle_managements*') ? 'menu-open' : '' }} {{ request()->is('admin/activate_mannual_settings*') ? 'menu-open' : '' }}">
                <a class="nav-link nav-dropdown-toggle">
                <i class="fa fa-cog" aria-hidden="true"></i>
                    <p>
                    &nbsp;<span>Settings</span>
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('user_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.commission_limit_managements.index") }}" class="nav-link {{ request()->is('admin/commission_limit_managements') || request()->is('admin/commission_limit_managements/*') ? 'active' : '' }}">
                            <p>
                                <span>Commission and Limit Management</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route("admin.admin_commissions.index") }}" class="nav-link {{ request()->is('admin/admin_commissions') || request()->is('admin/admin_commissions/*') ? 'active' : '' }}">

                            <p>

                                <span>Battle Commission Management</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route("admin.battle_managements.index") }}" class="nav-link {{ request()->is('admin/battle_managements') || request()->is('admin/battle_managements/*') ? 'active' : '' }}">

                            <p>
                                <span>Battle Management</span>
                            </p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route("admin.activate_mannual_settings.index") }}" class="nav-link {{ request()->is('admin/activate_mannual_settings') || request()->is('admin/activate_mannual_settings/*') ? 'active' : '' }}">

                            <p>
                                <span>Activate Manual Setting</span>
                            </p>
                        </a>
                    </li>

                    @endcan

                </ul>
            </li>
            @endcan

            @can('user_management_access')
            <li class="nav-item has-treeview {{ request()->is('admin/mannual_withdrawls*') ? 'menu-open' : '' }}">
                <a class="nav-link nav-dropdown-toggle">
                <i class="fa fa-credit-card" aria-hidden="true"></i>
                    <p>
                    &nbsp;<span>Manual Withdrawl</span>
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    @can('user_access')
                    <li class="nav-item">
                        <a href="{{ route("admin.mannual_withdrawls.index") }}" class="nav-link {{ request()->is('admin/mannual_withdrawls') || request()->is('admin/mannual_withdrawls/*') ? 'active' : '' }}">

                            <p>
                                <span>Manual Withdrawl Request</span>
                            </p>
                        </a>
                    </li>
                    @endcan

                </ul>
            </li>
            @endcan

            <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <p>
                        <i class="fas fa-sign-out-alt">

                        </i>
                        <span>{{ trans('global.logout') }}</span>
                    </p>
                </a>
            </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

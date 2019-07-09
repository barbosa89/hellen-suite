@extends('layouts.app')

@section('content')
{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div> --}}


  <div id="wrapper">
      <!-- Sidebar -->
      <ul class="sidebar navbar-nav">
          <li class="nav-item active">
              <a class="nav-link" href="index.html">
              <i class="fa fa-fw fa-tachometer"></i>
              <span>Dashboard</span>
              </a>
          </li>
          <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="pagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-folder"></i>
              <span>Pages</span>
              </a>
              <div class="dropdown-menu" aria-labelledby="pagesDropdown">
                  <h6 class="dropdown-header">Login Screens:</h6>
                  <a class="dropdown-item" href="login.html">Login</a>
                  <a class="dropdown-item" href="register.html">Register</a>
                  <a class="dropdown-item" href="forgot-password.html">Forgot Password</a>
                  <div class="dropdown-divider"></div>
                  <h6 class="dropdown-header">Other Pages:</h6>
                  <a class="dropdown-item" href="404.html">404 Page</a>
                  <a class="dropdown-item" href="blank.html">Blank Page</a>
              </div>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="charts.html">
              <i class="fa fa-fw fa-area-chart"></i>
              <span>Charts</span></a>
          </li>
          <li class="nav-item">
              <a class="nav-link" href="tables.html">
              <i class="fa fa-fw fa-table"></i>
              <span>Tables</span></a>
          </li>
      </ul>
      <div id="content-wrapper">
          <div class="container-fluid">
              <!-- Breadcrumbs-->
              <ol class="breadcrumb">
                  <li class="breadcrumb-item">
                      <a href="#">Dashboard</a>
                  </li>
                  <li class="breadcrumb-item active">Overview</li>
              </ol>
              <!-- Icon Cards-->
              <div class="row">
                  <div class="col-xl-3 col-sm-6 mb-3">
                      <div class="card text-white bg-primary o-hidden h-100">
                          <div class="card-body">
                              <div class="card-body-icon">
                                  <i class="fa fa-fw fa-comments"></i>
                              </div>
                              <div class="mr-5">26 New Messages!</div>
                          </div>
                          <a class="card-footer text-white clearfix small z-1" href="#">
                          <span class="float-left">View Details</span>
                          <span class="float-right">
                          <i class="fa fa-angle-right"></i>
                          </span>
                          </a>
                      </div>
                  </div>
                  <div class="col-xl-3 col-sm-6 mb-3">
                      <div class="card text-white bg-warning o-hidden h-100">
                          <div class="card-body">
                              <div class="card-body-icon">
                                  <i class="fa fa-fw fa-list"></i>
                              </div>
                              <div class="mr-5">11 New Tasks!</div>
                          </div>
                          <a class="card-footer text-white clearfix small z-1" href="#">
                          <span class="float-left">View Details</span>
                          <span class="float-right">
                          <i class="fa fa-angle-right"></i>
                          </span>
                          </a>
                      </div>
                  </div>
                  <div class="col-xl-3 col-sm-6 mb-3">
                      <div class="card text-white bg-success o-hidden h-100">
                          <div class="card-body">
                              <div class="card-body-icon">
                                  <i class="fa fa-fw fa-shopping-cart"></i>
                              </div>
                              <div class="mr-5">123 New Orders!</div>
                          </div>
                          <a class="card-footer text-white clearfix small z-1" href="#">
                          <span class="float-left">View Details</span>
                          <span class="float-right">
                          <i class="fa fa-angle-right"></i>
                          </span>
                          </a>
                      </div>
                  </div>
                  <div class="col-xl-3 col-sm-6 mb-3">
                      <div class="card text-white bg-danger o-hidden h-100">
                          <div class="card-body">
                              <div class="card-body-icon">
                                  <i class="fa fa-fw fa-life-ring"></i>
                              </div>
                              <div class="mr-5">13 New Tickets!</div>
                          </div>
                          <a class="card-footer text-white clearfix small z-1" href="#">
                          <span class="float-left">View Details</span>
                          <span class="float-right">
                          <i class="fa fa-angle-right"></i>
                          </span>
                          </a>
                      </div>
                  </div>
              </div>
              <!-- Area Chart Example-->
              <div class="card mb-3">
                  <div class="card-header">
                      <i class="fa fa-chart-area"></i>
                      Area Chart Example
                  </div>
                  <div class="card-body">
                      <canvas id="myAreaChart" width="100%" height="30"></canvas>
                  </div>
                  <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
              </div>
              <!-- DataTables Example -->
              <div class="card mb-3">
                  <div class="card-header">
                      <i class="fa fa-table"></i>
                      Data Table Example
                  </div>
                  <div class="card-body">
                      <div class="table-responsive">
                          <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                              <thead>
                                  <tr>
                                      <th>Name</th>
                                      <th>Position</th>
                                      <th>Office</th>
                                      <th>Age</th>
                                      <th>Start date</th>
                                      <th>Salary</th>
                                  </tr>
                              </thead>
                              <tfoot>
                                  <tr>
                                      <th>Name</th>
                                      <th>Position</th>
                                      <th>Office</th>
                                      <th>Age</th>
                                      <th>Start date</th>
                                      <th>Salary</th>
                                  </tr>
                              </tfoot>
                              <tbody>
                                  <tr>
                                      <td>Tiger Nixon</td>
                                      <td>System Architect</td>
                                      <td>Edinburgh</td>
                                      <td>61</td>
                                      <td>2011/04/25</td>
                                      <td>$320,800</td>
                                  </tr>
                                  <tr>
                                      <td>Garrett Winters</td>
                                      <td>Accountant</td>
                                      <td>Tokyo</td>
                                      <td>63</td>
                                      <td>2011/07/25</td>
                                      <td>$170,750</td>
                                  </tr>
                                  <tr>
                                      <td>Ashton Cox</td>
                                      <td>Junior Technical Author</td>
                                      <td>San Francisco</td>
                                      <td>66</td>
                                      <td>2009/01/12</td>
                                      <td>$86,000</td>
                                  </tr>
                                  <tr>
                                      <td>Cedric Kelly</td>
                                      <td>Senior Javascript Developer</td>
                                      <td>Edinburgh</td>
                                      <td>22</td>
                                      <td>2012/03/29</td>
                                      <td>$433,060</td>
                                  </tr>
                                  <tr>
                                      <td>Airi Satou</td>
                                      <td>Accountant</td>
                                      <td>Tokyo</td>
                                      <td>33</td>
                                      <td>2008/11/28</td>
                                      <td>$162,700</td>
                                  </tr>
                                  <tr>
                                      <td>Brielle Williamson</td>
                                      <td>Integration Specialist</td>
                                      <td>New York</td>
                                      <td>61</td>
                                      <td>2012/12/02</td>
                                      <td>$372,000</td>
                                  </tr>
                                  <tr>
                                      <td>Herrod Chandler</td>
                                      <td>Sales Assistant</td>
                                      <td>San Francisco</td>
                                      <td>59</td>
                                      <td>2012/08/06</td>
                                      <td>$137,500</td>
                                  </tr>
                                  <tr>
                                      <td>Rhona Davidson</td>
                                      <td>Integration Specialist</td>
                                      <td>Tokyo</td>
                                      <td>55</td>
                                      <td>2010/10/14</td>
                                      <td>$327,900</td>
                                  </tr>
                                  <tr>
                                      <td>Colleen Hurst</td>
                                      <td>Javascript Developer</td>
                                      <td>San Francisco</td>
                                      <td>39</td>
                                      <td>2009/09/15</td>
                                      <td>$205,500</td>
                                  </tr>
                                  <tr>
                                      <td>Sonya Frost</td>
                                      <td>Software Engineer</td>
                                      <td>Edinburgh</td>
                                      <td>23</td>
                                      <td>2008/12/13</td>
                                      <td>$103,600</td>
                                  </tr>
                                  <tr>
                                      <td>Jena Gaines</td>
                                      <td>Office Manager</td>
                                      <td>London</td>
                                      <td>30</td>
                                      <td>2008/12/19</td>
                                      <td>$90,560</td>
                                  </tr>
                                  <tr>
                                      <td>Quinn Flynn</td>
                                      <td>Support Lead</td>
                                      <td>Edinburgh</td>
                                      <td>22</td>
                                      <td>2013/03/03</td>
                                      <td>$342,000</td>
                                  </tr>
                                  <tr>
                                      <td>Charde Marshall</td>
                                      <td>Regional Director</td>
                                      <td>San Francisco</td>
                                      <td>36</td>
                                      <td>2008/10/16</td>
                                      <td>$470,600</td>
                                  </tr>
                                  <tr>
                                      <td>Haley Kennedy</td>
                                      <td>Senior Marketing Designer</td>
                                      <td>London</td>
                                      <td>43</td>
                                      <td>2012/12/18</td>
                                      <td>$313,500</td>
                                  </tr>
                                  <tr>
                                      <td>Tatyana Fitzpatrick</td>
                                      <td>Regional Director</td>
                                      <td>London</td>
                                      <td>19</td>
                                      <td>2010/03/17</td>
                                      <td>$385,750</td>
                                  </tr>
                                  <tr>
                                      <td>Michael Silva</td>
                                      <td>Marketing Designer</td>
                                      <td>London</td>
                                      <td>66</td>
                                      <td>2012/11/27</td>
                                      <td>$198,500</td>
                                  </tr>
                                  <tr>
                                      <td>Paul Byrd</td>
                                      <td>Chief Financial Officer (CFO)</td>
                                      <td>New York</td>
                                      <td>64</td>
                                      <td>2010/06/09</td>
                                      <td>$725,000</td>
                                  </tr>
                                  <tr>
                                      <td>Gloria Little</td>
                                      <td>Systems Administrator</td>
                                      <td>New York</td>
                                      <td>59</td>
                                      <td>2009/04/10</td>
                                      <td>$237,500</td>
                                  </tr>
                                  <tr>
                                      <td>Bradley Greer</td>
                                      <td>Software Engineer</td>
                                      <td>London</td>
                                      <td>41</td>
                                      <td>2012/10/13</td>
                                      <td>$132,000</td>
                                  </tr>
                                  <tr>
                                      <td>Dai Rios</td>
                                      <td>Personnel Lead</td>
                                      <td>Edinburgh</td>
                                      <td>35</td>
                                      <td>2012/09/26</td>
                                      <td>$217,500</td>
                                  </tr>
                                  <tr>
                                      <td>Jenette Caldwell</td>
                                      <td>Development Lead</td>
                                      <td>New York</td>
                                      <td>30</td>
                                      <td>2011/09/03</td>
                                      <td>$345,000</td>
                                  </tr>
                                  <tr>
                                      <td>Yuri Berry</td>
                                      <td>Chief Marketing Officer (CMO)</td>
                                      <td>New York</td>
                                      <td>40</td>
                                      <td>2009/06/25</td>
                                      <td>$675,000</td>
                                  </tr>
                                  <tr>
                                      <td>Caesar Vance</td>
                                      <td>Pre-Sales Support</td>
                                      <td>New York</td>
                                      <td>21</td>
                                      <td>2011/12/12</td>
                                      <td>$106,450</td>
                                  </tr>
                                  <tr>
                                      <td>Doris Wilder</td>
                                      <td>Sales Assistant</td>
                                      <td>Sidney</td>
                                      <td>23</td>
                                      <td>2010/09/20</td>
                                      <td>$85,600</td>
                                  </tr>
                                  <tr>
                                      <td>Angelica Ramos</td>
                                      <td>Chief Executive Officer (CEO)</td>
                                      <td>London</td>
                                      <td>47</td>
                                      <td>2009/10/09</td>
                                      <td>$1,200,000</td>
                                  </tr>
                                  <tr>
                                      <td>Gavin Joyce</td>
                                      <td>Developer</td>
                                      <td>Edinburgh</td>
                                      <td>42</td>
                                      <td>2010/12/22</td>
                                      <td>$92,575</td>
                                  </tr>
                                  <tr>
                                      <td>Jennifer Chang</td>
                                      <td>Regional Director</td>
                                      <td>Singapore</td>
                                      <td>28</td>
                                      <td>2010/11/14</td>
                                      <td>$357,650</td>
                                  </tr>
                                  <tr>
                                      <td>Brenden Wagner</td>
                                      <td>Software Engineer</td>
                                      <td>San Francisco</td>
                                      <td>28</td>
                                      <td>2011/06/07</td>
                                      <td>$206,850</td>
                                  </tr>
                                  <tr>
                                      <td>Fiona Green</td>
                                      <td>Chief Operating Officer (COO)</td>
                                      <td>San Francisco</td>
                                      <td>48</td>
                                      <td>2010/03/11</td>
                                      <td>$850,000</td>
                                  </tr>
                                  <tr>
                                      <td>Shou Itou</td>
                                      <td>Regional Marketing</td>
                                      <td>Tokyo</td>
                                      <td>20</td>
                                      <td>2011/08/14</td>
                                      <td>$163,000</td>
                                  </tr>
                              </tbody>
                          </table>
                      </div>
                  </div>
                  <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
              </div>
          </div>
          <!-- /.container-fluid -->
          <!-- Sticky Footer -->
          <footer class="sticky-footer">
              <div class="container my-auto">
                  <div class="copyright text-center my-auto">
                      <span>Copyright © Your Website 2019</span>
                  </div>
              </div>
          </footer>
      </div>
      <!-- /.content-wrapper -->
  </div>
  <!-- /#wrapper -->
  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
  <i class="fa fa-angle-up"></i>
  </a>
@endsection

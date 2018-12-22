<!doctype html>
<html lang="en">
<head>
    <title>{{ env('APP_NAME', 'CAD') }} - Login</title>
    <link rel="stylesheet" href="/css/bootstrap.css">
    <style type="text/css">
        html,
        body {
            background: #0f4c11; /* Old browsers */
            background: -moz-linear-gradient(45deg, #0f4c11 0%, #59a8e5 28%); /* FF3.6-15 */
            background: -webkit-linear-gradient(45deg, #0f4c11 0%,#59a8e5 28%); /* Chrome10-25,Safari5.1-6 */
            background: linear-gradient(45deg, #0f4c11 0%,#59a8e5 28%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0f4c11', endColorstr='#59a8e5',GradientType=1 ); /* IE6-9 fallback on horizontal gradient */
            height: 100%;
        }

        body {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }
        .form-signin .checkbox {
            font-weight: 400;
        }
        .form-signin .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;
        }
        .form-signin .form-control:focus {
            z-index: 2;
        }
        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }
        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .vertical-center {
            min-height: 100%;
            min-height: 100vh;

            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center vertical-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ env('APP_NAME', 'CAD') }} - Continue Login</div>

                <div class="card-body" id="cardbody">
                </div>
            </div>
        </div>
    </div>
</div>
<form method="post" action="/cad" id="loginform">
    @csrf
    <input type="hidden" id="formserver" name="server" value="-1">
    <input type="hidden" id="formdepartment" name="department" value="-1">
</form>
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/bootstrap.js"></script>
<script type="text/javascript">
    let serverid = undefined;
    const department = [
      'State',
      'Sheriff',
      'Police',
      'Dispatch',
      'Fire/EMS',
      'Civ'
    ];
    const loading = '<div class="row"><div class="col-md-12 text-center"><svg width="50" height="50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-double-ring"><circle cx="50" cy="50" fill="none" stroke-linecap="round" r="35" stroke-width="5" stroke="#0f4c11" stroke-dasharray="54.97787143782138 54.97787143782138" transform="rotate(102.738 50 50)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;360 50 50" keyTimes="0;1" dur="4.2s" begin="0s" repeatCount="indefinite"></animateTransform></circle><circle cx="50" cy="50" fill="none" stroke-linecap="round" r="29" stroke-width="5" stroke="#59a8e5" stroke-dasharray="45.553093477052 45.553093477052" stroke-dashoffset="45.553093477052" transform="rotate(-102.738 50 50)"><animateTransform attributeName="transform" type="rotate" calcMode="linear" values="0 50 50;-360 50 50" keyTimes="0;1" dur="4.2s" begin="0s" repeatCount="indefinite"></animateTransform></circle></svg> <h2>Loading...</h2></div></div>';
$(document).ready(() => {
  drawLoading();
  $.ajax({
    method: 'GET',
    url: '/api/servers',
  }).done((d) => {
    $('#cardbody').html(drawServers(d.servers));
  })
});
const drawLoading = () => {
  $('#cardbody').html(loading);
};
const drawServers = (serverList) => {
  let html = '<h4>Select a server</h4>';
  const btn = (id, server) => `<button class="serverBtn btn btn-primary btn-md btn-block" data-id="${id}">${server}</button>`;
  $.each(serverList, (i, v) => (html = html + btn(v.id, v.name)));
  return html;
};
const drawDept = (userroles) => {
  let html = '<h4>Select department</h4>';
  const btn = (name) => `<button class="deptBtn btn-primary btn-md btn-block" data-id="${ name.toLowerCase() }">${name}</button>`;
  const disabledbtn = (name) => `<button class="deptBtn btn-secondary btn-md btn-block" disabled>${name}</button>`;
  $.each(department, (i, v) => {
    if (!userroles.includes(v.toLowerCase())) {
      html = html + disabledbtn(v);
    } else {
      html = html + btn(v);
    }
  });
  return html;
};
$(document).on('click', '.serverBtn', (e) => {
  $('#formserver').val($(e).data("id"));
  drawLoading();
  $.ajax({
    method: 'get',
    url: '/api/account'
  }).done((d) => {
    console.dir(d);
    $("#cardbody").html(drawDept(d.user.roles));
  });
});
$(document).on('click', '.deptBtn', (e) => {
  $('#formdepartment').val($(e).data("id"));
  drawLoading();
  $('#loginform').submit();
})
</script>
</body>
</html>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ getSetting('nama_app')  }} - @yield('title')</title>
    <meta name="keywords" content="{{getSetting('keyword')}}"/>
    <meta name="description" content="{{getSetting('deskripsi')}}">
    <meta name="author" content="{{getSetting('author')}}">
    <link rel="shortcut icon" href="{{ url('uploads/'.getSetting('favicon'))}}"/>
    <link rel="stylesheet" href="{{assetku('assets/modules/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{assetku('assets/modules/fontawesome/css/all.min.css')}}">
    <style type="text/css">


        th {
            padding-left: 2px;
            padding-top: 0px;
            padding-bottom: 0px;
            padding-right: 0px;
            font-family: "Calibri";
            font-size: 13px;
            line-height: 17px
        }

        td {
            padding-left: 1px;
            padding-top: 0px;
            padding-bottom: 0px;
            padding-right: 1px;
            font-family: "Calibri";
            font-size: 14px;
            line-height: 15px
        }

        h2 {
            margin-bottom: 6px;
            padding-bottom: 0px
        }

        h3 {
            margin-bottom: 0px;
            padding-bottom: 0px;
            font-family: "Cambria";
        }

        h4 {
            margin-bottom: 6px;
            padding-bottom: 2px;
            font-size: 20px;
            font-family: "Cambria";
            line-height: 25px
        }


    </style>
    @stack('library-css')
</head>

<body style="padding: 20px">

<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
    <tbody>
    <tr>
        <td width="10%" rowspan="3" align="center" valign="middle"><img
                src="{{url('uploads/logolamtim.png')}}" width="80" height="96" alt=""/></td>
        <td width="80%" align="center" valign="middle"><h4 style="margin: 0px">{{getSetting('nama_app')}}</h4></td>
        <td width="10%" rowspan="3" align="center" valign="middle">&nbsp;</td>
    </tr>
    <tr>
        <td align="center" valign="middle"
            style="font-family: Cambria, 'Hoefler Text', 'Liberation Serif', Times, 'Times New Roman', serif; font-size: 30px;">
            <strong>{{getSetting('area')}}</strong></td>
    </tr>
    <tr>
        <td align="center" valign="middle">{{getSetting('alamat')}}</td>
    </tr>
    </tbody>
</table>

<hr/>
@yield('content')
<script>
    window.print();
</script>
@stack('scripts')
</body>
</html>

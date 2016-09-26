Imports System.Net
Imports System.IO
Imports System.Web
Imports System.Text

Module Database
    Public Sub insertDatabaseLoc(ByVal di_ch, ByVal imei, ByVal protocol, ByVal ip, ByVal port, ByVal lat, ByVal lng, ByVal altitude, ByVal angle, ByVal speed, ByVal dt_str, ByVal loc_valid, ByVal params, ByVal event_)
        ' check format if loc is not valid
        If loc_valid = "0" Then
            If lat = "0" Or lat.ToString = "" Then lat = "0"
            If lng = "0" Or lng.ToString = "" Then lng = "0"
            If altitude = "0" Or altitude.ToString = "" Then altitude = "0"
            If angle = "0" Or angle.ToString = "" Then angle = "0"
            If speed = "0" Or lat.ToString = "" Then speed = "0"
        End If

        ' convert date to suitable format
        If Not IsDate(dt_str) Then Exit Sub
        Dim dt As Date = dt_str
        dt_str = dt.ToString("yyyy-MM-dd HH:mm:ss")

        Dim m As String = "LOC - imei: " & imei & _
                        "; protocol: " & protocol & _
                        "; dt: " & dt_str & _
                        "; lat: " & lat & _
                        "; lng: " & lng & _
                        "; alt: " & altitude & _
                        "; ang: " & angle & _
                        "; spd: " & speed & _
                        "; loc_valid: " & loc_valid & _
                        "; pr: " & params & _
                        "; event: " & event_
        msg_green(m)
    End Sub

    Public Sub insertDatabaseNoLoc(ByVal di_ch, ByVal imei, ByVal protocol, ByVal ip, ByVal port, ByVal params, ByVal event_)
        Dim m As String = "NOLOC - imei: " & imei & _
                        "; protocol: " & protocol & _
                        "; pr: " & params & _
                        "; event: " & event_
        msg_green(m)
    End Sub
End Module

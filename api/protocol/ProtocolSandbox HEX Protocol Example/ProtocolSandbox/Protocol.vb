Imports System.Text

Module Protocol

    '############################################################################
    ' DO NOT CHANGE
    '############################################################################

    Public Class client
        Public client
    End Class

    Public Class clientInfo
        Public imei As String
        Public di_ch As String = "1"
        Public params As String

        Public protocol As String
        Public ip As String
        Public port As String
        Public debug As String

        Public Sub send(bytes)
            Dim str As String = System.Text.ASCIIEncoding.ASCII.GetString(bytes)
            msg("RESPONSE TO DEVICE: " & str)
        End Sub
    End Class

    Dim message As String = ""
    Dim messageHEX As String = ""

    Dim response
    Dim lat, lng, altitude, angle, speed, dt, loc_valid
    Dim params = ""
    Dim event_ = ""

    Dim e As New client

    Dim protocol As String = "HEX Protocol Example"

    '############################################################################
    ' DO NOT CHANGE
    '############################################################################

    Sub parseProtocol()

        '############################################################################
        ' DO NOT CHANGE
        '############################################################################

        e.client = New clientInfo

        '############################################################################
        ' DO NOT CHANGE
        '############################################################################

        '############################################################################
        ' RAW DATA FROM DEVICE
        '############################################################################

        ' DEVICE MAY SEND DATA IN ASCII OR HEX, DEPENDING ON THIS YOU MUST PARSE CORRECT VARIABLE IN "START PROTOCOL" SECTION

        ' ASCII DATA
        message = ""

        ' HEX DATA
        messageHEX = "464203000715ABF7213D40710E8B5F385613FC0F01C5ACF721993E710E8C5F385613FC0F0118AEF721383C710E8D5F385613FC0F010FAFF721F438710E8E5F385615FC0F015AAFF7211135710E8F5F385616FC0F013AAFF7217630710E905F38561AFC0F01DEAEF721112B710E915F38561FFC0F01"

        '############################################################################
        ' RAW DATA FROM DEVICE
        '############################################################################

       '############################################################################
        ' START PROTOCOL
        '############################################################################

        Try
            If Left(messageHEX, 4) = "4642" Then

                response = "01"
                e.client.Send(HexToBytes(response))

                Dim data_step = 5

                e.client.imei = Mid(messageHEX, data_step, 4)
                e.client.imei = Reverse2(e.client.imei)
                e.client.imei = HexToInt(e.client.imei)
                data_step += 4

                Dim event_num = Mid(messageHEX, data_step, 2)
                event_num = HexToInt(event_num)
                data_step += 2

                For i = 1 To CInt(event_num)
                    params = ""
                    event_ = ""

                    lat = Mid(messageHEX, data_step, 8)
                    lat = Reverse2(lat)
                    lat = HexToInt(lat)
                    lat = lat / 10000000
                    data_step += 8

                    lng = Mid(messageHEX, data_step, 8)
                    lng = Reverse2(lng)
                    lng = HexToInt(lng)
                    lng = lng / 10000000
                    data_step += 8

                    Dim timestamp = Mid(messageHEX, data_step, 8)
                    timestamp = Reverse2(timestamp)
                    timestamp = HexToInt(timestamp)
                    dt = ConvertTimestamp(timestamp * 1000)
                    data_step += 8

                    speed = Mid(messageHEX, data_step, 2)
                    speed = HexToInt(speed)
                    speed = Math.Floor(CDbl(speed))
                    data_step += 2

                    Dim fuel_pulses = Mid(messageHEX, data_step, 4)
                    fuel_pulses = Reverse2(fuel_pulses)
                    fuel_pulses = HexToInt(fuel_pulses)
                    params &= addParam("fp=" & fuel_pulses)
                    data_step += 4

                    Dim status = Mid(messageHEX, data_step, 2)
                    status = HexToBin(status)
                    status = Reverse(status)
                    data_step += 2

                    Dim acc = status(0)
                    params &= addParam("acc=" & acc)

                    Dim di1 = status(1)
                    params &= addParam("di1=" & di1)

                    Dim res1 = status(2)

                    Dim engine_running = status(3)
                    params &= addParam("engine_running=" & engine_running)

                    Dim do1 = status(4)
                    params &= addParam("do1=" & do1)

                    Dim res2 = status(5)

                    Dim gps_jammer = status(6)
                    params &= addParam("gps_jammer=" & gps_jammer)

                    Dim fuel_theft = status(7)
                    params &= addParam("fuel_theft=" & fuel_theft)

                    ' possible event_ variable strings: sos, bracon, bracoff, mandown, shock, tow, haccel, hbrake, hcorn, pwrcut, gpscut, lowdc, lowbat, jamming
                    event_ = ""

                    If lat <> "0" Then
                        loc_valid = "1"
                    Else
                        loc_valid = "0"
                    End If

                    insertDatabaseLoc(e.client.di_ch, e.client.imei, e.client.protocol, e.client.ip, e.client.port, lat, lng, altitude, angle, speed, dt, loc_valid, params, event_)
                Next
            End If

        Catch ex As Exception
            Dim err_msg = protocol & " ERROR: " & ex.Message
            Dim data = "ASCII: " & message & Environment.NewLine & "HEX: " & messageHEX

            writeErrorLog(err_msg, data)
        End Try

        '############################################################################
        ' END PROTOCOL
        '############################################################################
    End Sub

End Module

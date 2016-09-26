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

    Dim protocol As String = "ASCII Protocol Example"

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
        message = "$1,357804047969310,D001,AP29AW0963,01/01/13,13:24:47,1723.9582N,07834.0945E,00100,010,0,0,0,0,500,0008478660,1450,40,34,0,0,0,A#" & Environment.NewLine
        message &= "$1,357804047969310,D001,AP29AW0963,01/01/13,13:24:47,1723.9582N,07834.0945E,00100,010,0,0,0,0,500,0008478660,1450,40,34,0,0,0,A#" & Environment.NewLine
        message &= "$1,357804047969310,D001,AP29AW0963,01/01/13,13:24:47,1723.9582N,07834.0945E,00100,010,0,0,0,0,500,0008478660,1450,40,34,0,0,0,A#" & Environment.NewLine

        ' HEX DATA
        messageHEX = ""

        '############################################################################
        ' RAW DATA FROM DEVICE
        '############################################################################

        '############################################################################
        ' START PROTOCOL
        '############################################################################

        Try
            If Left(message, 1) = "$" Then

                message = message.Replace(Environment.NewLine, "")

                Dim receivedPackets = Split(message, "#")

                For i = 0 To receivedPackets.Length - 1
                    Dim receivedData = Split(receivedPackets(i), ",")

                    If receivedData.Length < 20 Then Exit For

                    params = ""
                    event_ = ""

                    e.client.imei = receivedData(1)

                    receivedData(4) = receivedData(4).Replace("/", "")
                    Dim year = "20" & Mid(receivedData(4), 5, 2)
                    Dim month = Mid(receivedData(4), 3, 2)
                    Dim day = Mid(receivedData(4), 1, 2)

                    receivedData(5) = receivedData(5).Replace(":", "")
                    Dim hour = Mid(receivedData(5), 1, 2)
                    Dim min = Mid(receivedData(5), 3, 2)
                    Dim sec = Mid(receivedData(5), 5, 2)

                    dt = year & "-" & month & "-" & day & " " & hour & ":" & min & ":" & sec

                    lat = receivedData(6)
                    lat = lat.Remove(lat.Length - 1)
                    lat = DMSToDD(lat)

                    If Right(receivedData(6), 1) = "S" Then 's/n
                        lat = "-" & lat
                    End If

                    lng = receivedData(7)
                    lng = lng.Remove(lng.Length - 1)
                    lng = DMSToDD(lng)

                    If Right(receivedData(7), 1) = "W" Then 'w/e
                        lng = "-" & lng
                    End If

                    speed = Math.Floor(CDbl(receivedData(8)) * 1.852)

                    angle = Math.Floor(CDbl(receivedData(9)))

                    altitude = 0

                    If receivedData.Length = 16 Then
                        Dim odo = receivedData(10)

                        Dim acc = receivedData(11)
                        params &= addParam("acc=" & acc)

                        Dim di1 = receivedData(12)
                        params &= addParam("di1=" & di1)

                        Dim di2 = receivedData(13)
                        params &= addParam("di2=" & di2)

                        Dim ai1 = receivedData(14)
                        params &= addParam("ai1=" & ai1)
                    ElseIf receivedData.Length = 23 Then
                        Dim bats = receivedData(10)
                        params &= addParam("bats=" & bats)

                        Dim acc = receivedData(11)
                        params &= addParam("acc=" & acc)

                        Dim di1 = receivedData(12)
                        params &= addParam("di1=" & di1)

                        Dim di2 = receivedData(13)
                        params &= addParam("di2=" & di2)

                        Dim ai1 = receivedData(14)
                        params &= addParam("ai1=" & ai1)

                        Dim rfid = receivedData(15)
                        params &= addParam("rfid=" & rfid)

                        Dim odo = receivedData(16)
                        params &= addParam("odo=" & odo)

                        Dim temp1 = receivedData(17)
                        params &= addParam("temp1=" & temp1)

                        Dim fuel1 = receivedData(18)
                        params &= addParam("fuel1=" & fuel1)

                        Dim accel = receivedData(19)
                        params &= addParam("accel=" & accel)

                        Dim do1 = receivedData(20)
                        params &= addParam("do1=" & do1)

                        Dim do2 = receivedData(21)
                        params &= addParam("do2=" & do2)
                    End If

                    ' possible event_ variable strings: sos, bracon, bracoff, mandown, shock, tow, haccel, hbrake, hcorn, pwrcut, gpscut, lowdc, lowbat, jamming
                    event_ = ""

                    Dim gps_status = Left(receivedData(receivedData.Length - 1), 1)

                    If gps_status = "A" Then
                        loc_valid = "1"
                    Else
                        loc_valid = "0"
                    End If

                    ' INSERT TO DATABASE
                    insertDatabaseLoc(e.client.di_ch, e.client.imei, e.client.protocol, e.client.ip, e.client.port, lat, lng, altitude, angle, speed, dt, loc_valid, params, event_)
                Next

                ' IF DEVICE NEEDS RESPONSE FOR ANY PACKET, IT CAN BE DONE USING BELOW CODE
                response = "EXAMPLE_REPONSE"
                Dim sendBytes As [Byte]() = Encoding.ASCII.GetBytes(response)
                e.client.Send(sendBytes)

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

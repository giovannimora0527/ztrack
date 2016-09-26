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

    Dim protocol As String = "PROTOCOL_NAME"

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
        messageHEX = ""

        '############################################################################
        ' RAW DATA FROM DEVICE
        '############################################################################

        '############################################################################
        ' START PROTOCOL
        '############################################################################

        Try

            ' INSERT TO DATABASE
            insertDatabaseLoc(e.client.di_ch, e.client.imei, e.client.protocol, e.client.ip, e.client.port, lat, lng, altitude, angle, speed, dt, loc_valid, params, event_)

            ' IF DEVICE NEEDS RESPONSE FOR ANY PACKET, IT CAN BE DONE USING BELOW CODE
            response = "EXAMPLE_REPONSE"
            Dim sendBytes As [Byte]() = Encoding.ASCII.GetBytes(response)
            e.client.Send(sendBytes)

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

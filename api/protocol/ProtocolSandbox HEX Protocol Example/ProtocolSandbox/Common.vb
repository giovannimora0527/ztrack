Imports System.Globalization
Imports System.Threading

Module Common

    Function GetUTCTime()
        Return Format(DateTime.UtcNow, "yyyy-MM-dd") & " " & Format(DateTime.UtcNow, "HH:mm:ss")
    End Function

    Function ConvertTimestamp(ByVal timestamp As Double)
        Dim dt = New DateTime(1970, 1, 1, 0, 0, 0).AddSeconds(timestamp / 1000)
        Return Format(dt, "yyyy-MM-dd") & " " & Format(dt, "HH:mm:ss")
    End Function

    Function DMSToDD(ByVal dms)
        Dim DD As Double

        DD = (dms \ 100) + ((dms - ((dms \ 100) * 100)) / 60)
        DD = String.Format("{0:n6}", CDbl(DD))
        Return DD
    End Function

    Function DMSToDD2(ByVal dms) 'ddmmss.ss
        Dim Degrees As Double
        Dim Minutes As Double
        Dim Seconds As Double
        Dim Seconds10th As Double
        Dim Seconds100th As Double
        Dim DD As Double

        If dms.ToString.Length = 9 Then
            Degrees = CDbl(Mid(dms, 1, 2))
            Minutes = CDbl(Mid(dms, 3, 2))
            Seconds = CDbl(Mid(dms, 5, 2))
            Seconds10th = CDbl(Mid(dms, 8, 1))
            Seconds100th = CDbl(Mid(dms, 9, 1))
        Else
            Degrees = CDbl(Mid(dms, 1, 3))
            Minutes = CDbl(Mid(dms, 4, 2))
            Seconds = CDbl(Mid(dms, 6, 2))
            Seconds10th = CDbl(Mid(dms, 9, 1))
            Seconds100th = CDbl(Mid(dms, 10, 1))
        End If

        DD = (Seconds100th / 360000) + (Seconds10th / 36000) + (Seconds / 3600) + (Minutes / 60) + Degrees
        DD = String.Format("{0:n6}", CDbl(DD))

        Return DD
    End Function

    Function Reverse(ByVal value As String) As String
        ' Convert to char array.
        Dim arr() As Char = value.ToCharArray()
        ' Use Array.Reverse function.
        Array.Reverse(arr)
        ' Construct new string.
        Return New String(arr)
    End Function

    Function Reverse2(ByVal value As String) As String
        Dim result = ""

        For y = 1 To Len(value) Step 2
            result &= Mid(value, Len(value) - y, 2)
        Next y

        Return result
    End Function

    Function Reverse3(ByVal value As String) As String
        Dim result = ""

        For y = 1 To Len(value) Step 4
            result &= Mid(value, y + 2, 2) & Mid(value, y, 2)
        Next y

        Return result
    End Function

    Function StrToHex(ByVal str)
        Dim result = ""

        For i As Integer = 1 To str.Length
            Dim ch = Asc(Mid(str, i, 1))
            result = result & Convert.ToString(ch, 16).PadLeft(2, "0"c)
        Next

        Return UCase(result)
    End Function

    Function HexToInt(ByVal hex)
        Return Convert.ToInt32(hex, 16)
    End Function

    Function HexToInt64(ByVal hex)
        Return Convert.ToInt64(hex, 16)
    End Function

    Function HexToBytes(ByVal s As String) As Byte()
        Dim i As Integer = 2
        While i < s.Length
            s = s.Insert(i, " ")
            i += 3
        End While

        Dim bytes As String() = s.Split(" "c)
        Dim retval(bytes.Length - 1) As Byte
        For ix As Integer = 0 To bytes.Length - 1
            retval(ix) = Byte.Parse(bytes(ix), System.Globalization.NumberStyles.HexNumber)
        Next
        Return retval
    End Function

    Function HexToBin(ByVal a As String) As String
        Dim strRet As String = String.Empty
        Dim strB As String

        For j As Integer = 0 To a.Length - 1
            strB = "0000" & Convert.ToString(Convert.ToInt32(a.Substring(j, 1), 16), 2)
            strRet &= strB.Substring(strB.Length - 4, 4)
        Next

        Return strRet
    End Function

    Function HEXToString(ByVal str)
        Dim num
        Dim value = ""
        For y = 1 To Len(str)
            num = Mid(str, y, 2)
            value = value & Chr(Val("&h" & num))
            y = y + 1
        Next y
        Return value
    End Function

    Function HexToSingle(ByVal hexValue As String) As Single
        Dim iInputIndex As Integer = 0
        Dim iOutputIndex As Integer = 0
        Dim bArray(3) As Byte
        For iInputIndex = 0 To hexValue.Length - 1 Step 2
            bArray(iOutputIndex) = Byte.Parse(hexValue.Chars(iInputIndex) & hexValue.Chars(iInputIndex + 1), Globalization.NumberStyles.HexNumber)
            iOutputIndex += 1
        Next
        Array.Reverse(bArray)
        Return BitConverter.ToSingle(bArray, 0)
    End Function

    Function BinToHex(ByVal BinNum As String) As String
        Dim BinLen As Integer, i As Integer
        Dim HexNum As Object = 0

        BinLen = Len(BinNum)
        For i = BinLen To 1 Step -1
            '     Check the string for invalid characters
            If Asc(Mid(BinNum, i, 1)) < 48 Or _
               Asc(Mid(BinNum, i, 1)) > 49 Then
                HexNum = ""
                Err.Raise(1002, "BinToHex", "Invalid Input")
            End If
            '     Calculate HEX value of BinNum
            If Mid(BinNum, i, 1) And 1 Then
                HexNum = HexNum + 2 ^ Math.Abs(i - BinLen)
            End If
        Next i
        '  Return HexNum as String
        BinToHex = Hex(HexNum)
    End Function

    Function IntToBin(ByVal value As Integer) As String
        Dim bin As String = Convert.ToString(value, 2)
        Return bin
    End Function

    Sub exitWithError(ByVal err_msg)
        msg_red("ERROR: " & err_msg)
        Thread.Sleep(5000)
        End
    End Sub

    Sub writeErrorLog(ByVal header, ByVal data)
        msg_red(header)
    End Sub

    Sub SetCulture()
        Dim cInfo = CultureInfo.CreateSpecificCulture("lt-LT")
        cInfo.NumberFormat.NumberDecimalSeparator = "."
        Thread.CurrentThread.CurrentCulture = cInfo
        Thread.CurrentThread.CurrentUICulture = cInfo
    End Sub

    Sub msg_red(ByVal msg As String)
        msg.Trim()
        Console.ForegroundColor = ConsoleColor.Red
        Console.WriteLine(msg)
        Console.ForegroundColor = ConsoleColor.Gray
    End Sub

    Sub msg_green(ByVal msg As String)
        msg.Trim()
        Console.ForegroundColor = ConsoleColor.Green
        Console.WriteLine(msg)
        Console.ForegroundColor = ConsoleColor.Gray
    End Sub

    Sub msg(ByVal msg As String)
        msg.Trim()
        Console.WriteLine(msg)
    End Sub

    Function addParam(ByVal param)
        Return param & "|"
    End Function

    Public Function distance(ByVal lat1 As Double, ByVal lon1 As Double, ByVal lat2 As Double, ByVal lon2 As Double) As Double
        Dim theta As Double = lon1 - lon2
        Dim dist As Double = Math.Sin(deg2rad(lat1)) * Math.Sin(deg2rad(lat2)) + Math.Cos(deg2rad(lat1)) * Math.Cos(deg2rad(lat2)) * Math.Cos(deg2rad(theta))
        dist = Math.Acos(dist)
        dist = rad2deg(dist)
        dist = dist * 60 * 1.1515
        dist = dist * 1.609344

        Return dist
    End Function

    Private Function deg2rad(ByVal deg As Double) As Double
        Return (deg * Math.PI / 180.0)
    End Function

    Private Function rad2deg(ByVal rad As Double) As Double
        Return rad / Math.PI * 180.0
    End Function

    Public Function GetCRC16(ByVal data As Byte(), ByVal length As Integer) As UShort
        Dim fcs As UShort = &HFFFF
        Dim ret As UShort
        Dim strTmp As String
        Dim crcCode As String
        For i As Integer = 0 To length - 1
            fcs = CUShort(CUShort(fcs >> 8) Xor crctab16((fcs Xor data(i)) And &HFF))
        Next
        ret = CUShort(Not fcs)
        strTmp = ret.ToString("X4")
        crcCode = strTmp.Substring(2, 2) + " " + strTmp.Substring(0, 2)
        Return CUShort(Not fcs)
    End Function

    Dim crctab16 As UShort() = {&H0, &H1189, &H2312, &H329B, &H4624, &H57AD, _
                            &H6536, &H74BF, &H8C48, &H9DC1, &HAF5A, &HBED3, _
                            &HCA6C, &HDBE5, &HE97E, &HF8F7, &H1081, &H108, _
                            &H3393, &H221A, &H56A5, &H472C, &H75B7, &H643E, _
                            &H9CC9, &H8D40, &HBFDB, &HAE52, &HDAED, &HCB64, _
                            &HF9FF, &HE876, &H2102, &H308B, &H210, &H1399, _
                            &H6726, &H76AF, &H4434, &H55BD, &HAD4A, &HBCC3, _
                            &H8E58, &H9FD1, &HEB6E, &HFAE7, &HC87C, &HD9F5, _
                            &H3183, &H200A, &H1291, &H318, &H77A7, &H662E, _
                            &H54B5, &H453C, &HBDCB, &HAC42, &H9ED9, &H8F50, _
                            &HFBEF, &HEA66, &HD8FD, &HC974, &H4204, &H538D, _
                            &H6116, &H709F, &H420, &H15A9, &H2732, &H36BB, _
                            &HCE4C, &HDFC5, &HED5E, &HFCD7, &H8868, &H99E1, _
                            &HAB7A, &HBAF3, &H5285, &H430C, &H7197, &H601E, _
                            &H14A1, &H528, &H37B3, &H263A, &HDECD, &HCF44, _
                            &HFDDF, &HEC56, &H98E9, &H8960, &HBBFB, &HAA72, _
                            &H6306, &H728F, &H4014, &H519D, &H2522, &H34AB, _
                            &H630, &H17B9, &HEF4E, &HFEC7, &HCC5C, &HDDD5, _
                            &HA96A, &HB8E3, &H8A78, &H9BF1, &H7387, &H620E, _
                            &H5095, &H411C, &H35A3, &H242A, &H16B1, &H738, _
                            &HFFCF, &HEE46, &HDCDD, &HCD54, &HB9EB, &HA862, _
                            &H9AF9, &H8B70, &H8408, &H9581, &HA71A, &HB693, _
                            &HC22C, &HD3A5, &HE13E, &HF0B7, &H840, &H19C9, _
                            &H2B52, &H3ADB, &H4E64, &H5FED, &H6D76, &H7CFF, _
                            &H9489, &H8500, &HB79B, &HA612, &HD2AD, &HC324, _
                            &HF1BF, &HE036, &H18C1, &H948, &H3BD3, &H2A5A, _
                            &H5EE5, &H4F6C, &H7DF7, &H6C7E, &HA50A, &HB483, _
                            &H8618, &H9791, &HE32E, &HF2A7, &HC03C, &HD1B5, _
                            &H2942, &H38CB, &HA50, &H1BD9, &H6F66, &H7EEF, _
                            &H4C74, &H5DFD, &HB58B, &HA402, &H9699, &H8710, _
                            &HF3AF, &HE226, &HD0BD, &HC134, &H39C3, &H284A, _
                            &H1AD1, &HB58, &H7FE7, &H6E6E, &H5CF5, &H4D7C, _
                            &HC60C, &HD785, &HE51E, &HF497, &H8028, &H91A1, _
                            &HA33A, &HB2B3, &H4A44, &H5BCD, &H6956, &H78DF, _
                            &HC60, &H1DE9, &H2F72, &H3EFB, &HD68D, &HC704, _
                            &HF59F, &HE416, &H90A9, &H8120, &HB3BB, &HA232, _
                            &H5AC5, &H4B4C, &H79D7, &H685E, &H1CE1, &HD68, _
                            &H3FF3, &H2E7A, &HE70E, &HF687, &HC41C, &HD595, _
                            &HA12A, &HB0A3, &H8238, &H93B1, &H6B46, &H7ACF, _
                            &H4854, &H59DD, &H2D62, &H3CEB, &HE70, &H1FF9, _
                            &HF78F, &HE606, &HD49D, &HC514, &HB1AB, &HA022, _
                            &H92B9, &H8330, &H7BC7, &H6A4E, &H58D5, &H495C, _
                            &H3DE3, &H2C6A, &H1EF1, &HF78}

    Public Class CRC16_CCITT
        Private Shared CRC16_CCITT_Tab(255) As UInt16
        Shared Sub New()
            CRC16_CCITT_InitTable()
        End Sub
        Public Shared Function Calculate(ByVal Input As Byte(), Optional ByVal CRC As UInt16 = &HFFFF) As UInt16
            Dim b() As Byte = Input
            For Each BT As Byte In b
                CRC = (CRC << 8) Xor CRC16_CCITT_Tab((CRC >> 8) Xor System.Convert.ToUInt16(BT))
            Next
            Return CRC
        End Function
        Public Overloads Shared Function ToString(ByVal CRC As UInt16) As String
            Dim result = Chr(CRC >> 8) & Chr(CRC And &HFF)

            Return result
        End Function
        Private Shared Sub CRC16_CCITT_InitTable()
            Dim CRC As UInt16
            For i As UInt16 = 0 To 255
                CRC = i << 8
                For j As Integer = 0 To 7
                    If (CRC And &H8000) = &H8000 Then
                        CRC = (CRC << 1) Xor &H1021
                    Else
                        CRC = CRC << 1
                    End If
                Next
                CRC16_CCITT_Tab(i) = CRC
            Next
        End Sub
    End Class
End Module

# google-spreadsheet-to-php-array-class
This class return google spreadsheet data to php array via Google Sheets API v4

# Dependency
This class using the Guzzle HTTP Client library: https://github.com/guzzle/guzzle

# Usage
1) Put the class into project
2) Set your Google Sheets API key (created via google developer console)
3) Just call the "get_sheet_data" method with valid URL of google docs spreadsheet

# Important notes
Valid google docs URL need to be in this format: "https://docs.google.com/spreadsheets/d/1JF2AKbhgvXDUbK7W4JRNwid-85IlgRT6gfdfg56654d"

The URLs must have read access

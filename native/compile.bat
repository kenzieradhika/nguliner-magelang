@echo off
rem Compile ngsecurity.dll with MSVC (Visual Studio 2022).
rem Usage: compile.bat  (run from native/ or project root)
cd /d "%~dp0"
call "C:\Program Files\Microsoft Visual Studio\2022\Community\VC\Auxiliary\Build\vcvars64.bat" >nul
cl /nologo /O2 /EHsc /LD ngsecurity.cpp /Fe:ngsecurity.dll
if errorlevel 1 (
    echo [FAIL] compile error
    exit /b 1
)
echo [OK] native\ngsecurity.dll

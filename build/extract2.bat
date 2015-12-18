
FOR %%f IN (.) DO (
    ECHO %%f|FINDSTR "_free\.zip$" >nul
    IF %errorlevel% == 0 (
        ECHO FREE
    ) ELSE (
        ECHO %%f|FINDSTR "_pro\.zip$" >nul
        IF %errorlevel% == 0 (
            ECHO PRO
        )
        ELSE ECHO OTHER
    )
)
#!/usr/bin/env bash

### Start project
docker-compose build
docker-compose up -d
bin/generate-jwt
bin/reset-env

## Obtain admin token
curl -X POST "http://localhost/api/token" \
-H "Accept: application/json" \
-H "Content-Type: application/json" -d '
{
  "email": "super@admin.pl",
  "password": "password"
}
' | json

## Create user
curl -X POST "http://localhost/api/users" \
-H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NTA5MzEwNTcsImV4cCI6MTU1MDkzNDY1Nywicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InN1cGVyQGFkbWluLnBsIn0.O2xbl2VRgzc-1ZH_YPdsGh3GSuSL9aoAYltUvc-kJb9pDCHGPcAdoib5ndjat04zyjpMtGChGfg1FDSi0I6y6_szR4dErEofFmfvCB0UoV_yaCqk6ltf9nXGVyE3wIN2g7gMZIvwZ3FoXF-YAlbHB_yC70qs_GWXwT6J_hQfC03ctTTrsxzu84TWahI1RtJV-_hF5OVFERZQn6DiVMfeOV6tv3JADTEZeoRVrPyPYJu8EG33c6vBtaeD4ISCaLue6Q_mK0Nla751oP5MZDgbBvihi_EeT0jODwO-Taa-jN7PrcJ34S5MUxMsEdo7XPLuLjfLBQwIaLDujazFfZgGGwteANHaJwrX5ZoU0Kn9S0KFd7tk7nzBjIpiBhZj0JOtBovzmjn3AIz61mYIvdzpfUNNuJseOfzH28IZjojMIqhDW40HfySodqmXqnQMr432IwzHi4zOlOMacGrDhFmV1fu3BtU5ynoUhJsTiYl8hWuPxLOsZbKoPxPPi8msfEz4a0PbCj7AU74GfQamJBlL4UYDtU8OBhTPIOPb2LSADb7V2CGQhXJpuY1sUm-6n9ecjqATWEClARuP3v4XpaePKNcN6fOctamjdAcvjOzndjE10-zSCCg7tmiDAaGoae2WvFJzW-S-S-VDkhI9xXTXeJgf1oWGTx01SmzcW7v1Lw4" \
-H "Accept: application/json" \
-H "Content-Type: application/json" -d '
{
    "email": "test@test.pl",
    "plainPassword": "test@test",
    "roles": [
        "ROLE_USER"
    ]
}
' | json

## Obtain created user token
curl -X POST "http://localhost/api/token" \
-H "Accept: application/json" \
-H "Content-Type: application/json" -d '
{
  "email": "test@test.pl",
  "password": "test@test"
}
' | json

## Create review
curl -X POST "http://localhost/api/reviews" \
-H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NTA5MzE1MzAsImV4cCI6MTU1MDkzNTEzMCwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdEB0ZXN0LnBsIn0.JJ82xiJ5Zswu5_DyKqdBtAQBhSNwMJr-KWbJrqQ29JUnpe1mZokvteOYMJu-scvw4hwbRVtzFs_gXM697dYeLdrM8wrCAS8ZJbmoW7LEAklDvzYs9neQuS8MeQwZqNecmkSWjzQj3cDwOfld8O98xq7v-nb_26B5dY5OjpnzO85x86pTuNVc5ag3VssiP9nhvNy59-FBuNHjq-CAbp3yryCTxu7DQAi59vIx2pAmdFLmUxeTptMIfVRs4Qvy6R-FECF1mnNyezsjvUqtYJZhlBmMEe29qZile88nPATz09QAYM5fTkoRXd1PZfOIG9uU4DmMjhhGf9cI6UGjqbVGj4RYLkJJ8F9QEw_ByGhc622V6nO8Vzg2QQziFijQrJ9SavLkdrEZR0Egup-wCGgzYISst_eLQSrWHg5yZqXaMobXAgaXRS5HmvGv3Rm_sAcdfZfCpnefnDtKXOHe9tVyZKy50UNz3eA784hIYANj0P8lOI7BQStxPdM4uyys1Kw62AjpIucofxrPh0DPsdJK3m-g5uOvXJB6BE4l4Be4bUB_X1gg7rxwDpoenq5bXlwEn2Zvpe3zrK0buQ-uIc489gKKikUu4-uzYQWivJ5FS-__EWinQ0zzGqIcBcRLg8yf4Py3A2sNHiDqQLy1ctfA2regBW6v7_jbNeQkjQyhfHI" \
-H "Accept: application/json" \
-H "Content-Type: application/json" -d '
{
    "gitRepositoryUrl": "https://github.com/k911/swoole-bundle.git",
    "currentCommitHash": "c88fcf229e59edf8c2100b907a3dca0d14a78384",
    "enabledChecks": [
        "ESLint",
        "PHP-CS-Fixer",
        "PHPStan"
    ]
}
' | json

## Get reviews
curl "http://localhost/api/reviews" \
-H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NTA5MzMzMDUsImV4cCI6MTU1MDkzNjkwNSwicm9sZXMiOlsiUk9MRV9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InN1cGVyQGFkbWluLnBsIn0.ITXY1NriUeJIzpXauNIHNRdtN2S3p_X0-kS8anuWYOCkIXiV7k6kjBKThUz9qyd_WXODzzAaQeVN-LPy9ubJ-r3IEXT3uXHUeY_8kBkFFV2VTgAaxmUy9SqETok9G7vKAq5jCnBP2qh-we5JPg4nv0v8XE0cTOUzBPSg_joEUYqw81WPNNReQ_TY2dceFrfc13j5BlBSfcFesIJT1lzedAnhc9hRGnVVCUo4VMjQhsU0OjGcGIAkf4avSv-3oyHmHbdeIfKyR8NhjcZG8Kj2klgx3zADy9hULzsl3lA8qM7A6NkXqxerhZDHrwYB7yIEDVFddQhxrgQdkkR7ALwpV7lJTDujKoUdHQOZwQSWVT49U7J1r2MIcli80UKopRieIPM4CFxXZxNnASXuy-ZHsf1Qzuy_QCFHaGUnKOulm3CDjh_LGWWC9v9AUu8usKMydFmzU_aRPYiM9rcK-_HFLr2Xb5yy_MpHtxmwaj86eSp3hSEuSuI7Z-TBrReseJqHGQ9PzKnOwfg0hK89UZ4YXcMm8x5eeFvA5xT0z0H-4IXG3GRIaraz7KsqwIB20vKGguLaF2P2GH9JIN2-Ytc8f8lry1oah-39xAKWRPAww6qf1g6DLzItYVt0eTrcXP2ba46klqsA5aJc8osVFAqae0Y5cvzSL5ozAhruPyihigE" \
-H "Accept: application/json" | json

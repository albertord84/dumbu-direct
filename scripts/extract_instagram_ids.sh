#!/bin/sh

msg=`echo {"datetime":"20170714_161319","uid":"4492293740","pks":["__PK__"],"message":"Ganhe milhares de seguidores por areas de interesse, turbine seu negocio, teste 7 dias de graça sem compromisso algum usando o código promocional INSTA-DIRECT, esta promoção chega por você ter acessado ao nosso sistema anteriormente: www.dumbu.pro"}`

grep insta_id $1 | sed s/\<field\ name=\"insta_id\"\>//g | sed s/\<\\/field\>//g | awk '{print $1}'


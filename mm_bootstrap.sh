#!/bin/bash
# $0 ： 命令本身
# $1, $2, $n ： 第n个参数.
# $#  参数的个数，不包括命令本身.
# $@ ：参数本身的列表，也不包括命令本身
# $* ：和$@相同，但"$*" 和 "$@"(加引号)并不同，"$*"将所有的参数解释成一个字符串，而"$@"是一个参数

# 设置为开发模式
function change_env_dev()
{
    echo "设置为开发模式"
    cp -r ./mmconf/dev/* ./mmconf/dist/
}

# 设置为正式环境模式
function change_env_product()
{
    echo "设置为正式环境模式(提交模式)"
    cp -r ./mmconf/product/* ./mmconf/dist/
}

# 初次创建时的初始化操作
function mm_init()
{
    echo "初始化"
    if [ ! -d "./mmconf" ]; then
      mkdir ./mmconf
      echo "创建mmconf文件夹"
    fi
    if [ ! -d "./mmconf/dist" ]; then
      mkdir ./mmconf/dist
      echo "创建mmconf发布文件夹"
    fi
    if [ ! -d "./mmconf/dev" ]; then
      mkdir ./mmconf/dev
      echo "创建mmconf开发配置文件夹"
      cp -r ./mmconf/dist/* ./mmconf/dev
    fi
    if [ ! -d "./mmconf/product" ]; then
      mkdir ./mmconf/product
      echo "创建mmconf正式环境配置文件夹"
      cp -r ./mmconf/dist/* ./mmconf/product
    fi
}

function help()
{
    echo "MM项目工具脚本"
    echo "参数：dev 设置为开发模式"
    echo "参数：product 设置为生产模式"
    echo "参数：init 初始化(第一次克隆代码时必须执行)"
}

num=$#

if [ $num -eq 1 ]
then
    case $1 in
        "init")
            mm_init
            ;;
        "dev")
            change_env_dev
            ;;
        "product")
            change_env_product
            ;;
        ?)
            help
            ;;
        esac
else
    help
fi

exit 0

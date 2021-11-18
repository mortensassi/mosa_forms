const fetchData = async (url, method = 'GET') => {
    const res = await fetch(url, {
        method
    })

    return await res.json()
}

export default fetchData
